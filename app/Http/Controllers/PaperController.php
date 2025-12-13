<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaperAssessmentRequest;
use App\Http\Requests\MarkEntryRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; 

class PaperController extends Controller
{
    
    public function assignAssessment(PaperAssessmentRequest $request){

        $paper_id=$request->paper_id;
        if (!$this->checkFacultyPaperAssignment($paper_id)) {
            return response()->json(['message' => 'Unauthorized']);
         }          

     try{
          $inserted= DB::table('paper_assessment')->insert([

            'paper_id' =>$request->paper_id,
            'assessment_type_id' =>$request->assessment_type_id,
            'max_mark'=>$request->max_mark,
            'scale_id'=>$request->scale_id,
        ]);

        if($inserted){
            return response()->json(['message'=>'Successfully created assessment for this paper']);
        }
    } catch(QueryException $e){

        if($e->errorInfo[1]==1062){
            return response()->json(['message'=>'This assessment already created for this paper']);
        }

        return response()->json(['message'=>'Failed to create assessment for this paper']);

    }
}                     /////////////////////////////////

    public function markEntry(MarkEntryRequest $request){
        $paper_assessment_id=$request->paper_assessment_id;
        $marks=$request->marks;

        $max_mark=DB::table('paper_assessment')
                    ->where('id',$paper_assessment_id)
                    ->value('max_mark');

        $scale_id=DB::table('paper_assessment')
                    ->where('id',$paper_assessment_id)
                    ->value('scale_id');
                    

        $paper_id=DB::table('paper_assessment')
                    ->where('id',$paper_assessment_id)
                    ->value('paper_id');

         if (!$this->checkFacultyPaperAssignment($paper_id)) {
            return response()->json(['message' => 'Unauthorized']);
         }

        DB::beginTransaction();
        try{
            $ignored=[];
            $updated=[];

            foreach($marks as $m){

                $enrolledpaper=DB::table('student_paper')
                        ->where('student_id',$m['student_id'])
                        ->where('paper_id',$paper_id)
                        ->exists();
                if(!$enrolledpaper){
                    $ignored[] =[
                        'student_id' => $m['student_id'],
                        'message'    => "This student hadn't enrolled this paper"
                ];
                continue;
                } 
                
                $enteredalready=DB::table('student_assessment_mark')
                        ->where('student_id',$m['student_id'])
                        ->where('paper_assessment_id',$paper_assessment_id)
                        ->first();
                if( $enteredalready){
                    DB::table('student_assessment_mark')
                      ->where('id',$enteredalready->id)
                      ->update([
                            'mark'=>$m['mark']
                      ]);
                    $updated[]=[
                        'student_id' => $m['student_id'],
                ];
                continue;
                }
                
                 $percentage=  $m['mark']/$max_mark *100;
        
                 $range = DB::table('scale_ranges')
                        ->where('scale_id', $scale_id)
                        ->where('%_from', '<=', $percentage)
                        ->where('%_to', '>', $percentage)
                        ->first();
                    
                DB::table('student_assessment_mark')->insert([
                    'paper_assessment_id'=> $paper_assessment_id,
                    'student_id'         =>$m['student_id'],
                    'mark'               =>$m['mark'],
                    'grade'              =>$range->grade,
                    'gradepoint'         =>$range->gradepoint
                ]);
            }
             DB::commit(); 
        }catch(\Exception $e){
            DB::rollback();

            return response()->json([
                'message'  =>"Failed to insert",
                'error'    =>$e->getMessage()
            ]);
        }
        return response()->json([
                'message'  =>"Mark insertion completed",
                'ignored'  =>$ignored,
                'updated'  =>$updated
            ]);
    }

                   /////////////////////////////////////////////// 
        
    public function showMarklist(Request $request){

         $validator=Validator::make($request->query(),[
            'paper_assessment_id' => 'required|exists:paper_assessment,id',
         ]);

         if($validator->fails()){
            return response()->json([
                "message" =>"validation failed",
                "errors"  =>$validator->errors()
            ]);
         }

        $paper_assessment_id=$request->query('paper_assessment_id');

        $data= DB::table('student_assessment_mark')
            ->where('paper_assessment_id',$paper_assessment_id)
            ->select('student_id','mark','grade','gradepoint')
            ->get();

            return response()->json([
                'marks'=>$data
            ]);
    }
                   
                    /////////////////////////////////////////////

        public function markFinalise(Request $request){

             $request->validate([
                'paper_id'=>'required|exists:papers,id',
             ]);
        
              $paper_id=$request->paper_id;
              $max_paper_mark=DB::table('papers')
                            ->where('id',$paper_id)
                            ->value('max_mark');

             $scale_id=DB::table('papers')
                            ->where('id',$paper_id)
                            ->value('scale_id');              

             $max_assessments_mark=DB::table('paper_assessment')
                                   ->where('paper_id',$paper_id)
                                    ->sum('max_mark');
                                  
              if (!$this->checkFacultyPaperAssignment($paper_id)) {
                     return response()->json(['message' => 'Unauthorized']);
                }

              $data= DB::table('student_paper as sp')
                    ->leftjoin('paper_assessment as pa','sp.paper_id','=','pa.paper_id')
                    ->leftjoin('student_assessment_mark as sam',function($join){
                        $join->on('sam.paper_assessment_id','=','pa.id')
                             ->on('sp.student_id','=','sam.student_id'); 
                            })
                    ->where ("pa.paper_id", $paper_id)
                    ->selectRaw('sp.student_id, COALESCE(SUM(sam.mark), 0) as total')
                    ->groupby('sp.student_id')
                    ->get();

                foreach($data as $d) {
                        $total=( $d->total)*$max_paper_mark/$max_assessments_mark;
                        $percentage=  $total/$max_paper_mark *100;
        
                        $range = DB::table('scale_ranges')
                                ->where('scale_id', $scale_id)
                                ->where('%_from', '<=', $percentage)
                                ->where('%_to', '>', $percentage)
                                ->first();
  
                       DB::table('student_paper_mark')->insert([
                             'student_id' => $d->student_id,
                             'paper_id'   => $paper_id,
                             'mark'       => $total,
                             'grade'      => $range->grade,
                             'gradepoint' => $range->gradepoint
            
                       ]); 
                  }   

                return response()->json([
                   'message'=> "Mark successfully finalised"
                ]);    
         }

                    /////////////////////////////////////////////

    public function checkfacultypaperassignment($paper_id){

        $faculty_Id = auth('api')->id();
        return DB::table('paper_faculty')
                ->where('paper_id',$paper_id)
                ->where('faculty_id', $faculty_Id)
                ->exists();              
    }

}
