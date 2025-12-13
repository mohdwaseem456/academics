<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgrammeController extends Controller
{
    
  public function showProgrammeStudents(Request $request){

    $request->validate([
        'programme_id'=>'required|exists:programmes,id'
    ]);

    $programme_id=$request->query('programme_id');

    $datas=DB::table('batches as b')
                ->leftjoin('admissions as a','a.batch_id','=','b.id')
                ->leftjoin('students as s','a.student_id','=','s.student_id')
                ->leftjoin('student_paper as sp','a.student_id','=','sp.student_id')
                ->leftjoin('papers as p','sp.paper_id','=','p.id')
                ->where('b.programme_id',$programme_id)
                ->select('b.id as batch_id',
                    's.student_id as student_id' ,
                     DB::raw("concat(s.first_name,' ',s.last_name) as full_name"),
                     'p.name as paper_name'
                     )
                ->get();

    $prev_batch_id=0;  
    $prev_student_id=0;
    $results=[];
    $i=-1;
    $j=-1;
            
 
    foreach($datas as $row){

        $batch_id = $row->batch_id;
        $student_id = $row->student_id;

        if($batch_id === $prev_batch_id){

            if($student_id == $prev_student_id){

                $results[$i]['students'][$j]['papers'][] = $row->paper_name;

            } else {

                $results[$i]['students'][] = [
                    'student_id' => $row->student_id,
                    'student_name' => $row->full_name,
                    'papers' => [$row->paper_name]
                ];

                $j++;
                $prev_student_id = $student_id;
            }

        } else {
            $results[] = [
                'batch_id' => $batch_id,
                'students' => [
                    [
                        'student_id' => $row->student_id,
                        'student_name' => $row->full_name,
                        'papers' => [$row->paper_name]
                    ]
                ]
            ];

            $i++;
            $j = 0;
            $prev_batch_id = $batch_id;
            $prev_student_id = $student_id;
        }
    }

    return response()->json($results);
  

    }
    }