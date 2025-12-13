<?php

namespace App\Http\Controllers;

use App\Models\admission;
use App\Models\batch;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\ShowProgrammeAttendanceRequest;
use App\Http\Requests\ShowBatchAttendenceRequest;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function bulkMark(AttendanceRequest $request)
    {
        $facultyId = auth('api')->id();
        $data = $request->validated();

        $programmeType = $data['programme_type'];
        $programmeId = $data['programme_id'];

        $common = [
            'programme_id' => $programmeId,
            'programme_type' => $programmeType,
            'date' => $data['date'],
            'hour' => $data['hour']
        ];

        if ($programmeType == 1) {
            $isFacultyAssigned = DB::table('paper_faculty')
                ->where('paper_id', $programmeId)
                ->where('faculty_id', $facultyId)
                ->exists();

            if (!$isFacultyAssigned) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $students = $data['students'];
        $studentIds = collect($students)->pluck('student_id')->toArray();

        $existing = DB::table('attendances')
            ->where('date', $common['date'])
            ->where('hour', $common['hour'])
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id')
            ->toArray();

        $insert = [];
        $updates = [];
        $ignored = [];

        foreach ($students as $student) {
            $sid = $student['student_id'];
            $attendance = $student['attendance'];

            $isEnrolled = DB::table('student_paper')
                ->where('student_id', $sid)
                ->where('paper_id', $programmeId)
                ->exists();

            if ($programmeType == 1 && !$isEnrolled) {
                $ignored[] = [
                    'student_id' => $sid,
                    'reason' => 'Student not enrolled for this paper'
                ];
                continue;
            }

            if (!isset($existing[$sid])) {
                $insert[] = [
                    'programme_id' => $programmeId,
                    'programme_type' => $programmeType,
                    
                    'student_id' => $sid,
                    'attendance' => $attendance,
                    'faculty_id' => $facultyId,
                    'date' => $common['date'],
                    'hour' => $common['hour'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                continue;
            }

            $old = (object)$existing[$sid];

            if ($old->programme_type == $programmeType && $old->programme_id == $programmeId) {
                if ($programmeType == 2) {
                    if ($old->attendance == 1 && $attendance == 0) {
                        $ignored[] = [
                            'student_id' => $sid,
                            'reason' => 'Event attendance cannot change 1 to 0'
                        ];
                    } else {
                        $updates[] = [
                            'id' => $old->id,
                            'attendance' => $attendance,
                            'faculty_id' => $facultyId,
                            'updated_at' => Carbon::now()
                        ];
                    }
                } else {
                    $updates[] = [
                        'id' => $old->id,
                        'attendance' => $attendance,
                        'faculty_id' => $facultyId,
                        'updated_at' => Carbon::now()
                    ];
                }
            } else {
                $ignored[] = [
                    'student_id' => $sid,
                    'reason' => 'Attendance already marked for another programme'
                ];
            }
        }

        DB::beginTransaction();

        if (!empty($insert)) {
            DB::table('attendances')->insert($insert);
        }

        if (!empty($updates)) {
            foreach ($updates as $u) {
                $id = $u['id'];
                unset($u['id']);
                DB::table('attendances')->where('id', $id)->update($u);
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Attendance processed',
            'created_count' => count($insert),
            'updated_count' => count($updates),
            'ignored_count' => count($ignored),
            'ignored_records' => $ignored
        ], 201);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////

 public function showBatchAttendance(Request $request){

    $request->validate([
        'batch_id'=>'required|exists:batches,id',
        'date'    => 'required|date',
          'hr'     => 'required|min:1|max:8'

    ]);

    $batch_id=$request->query('batch_id');
    $date=$request->query('date');
    $hour=$request->query('hr');

   $students = DB::table('admissions')
    ->leftJoin('attendances', function ($join) use ($date, $hour, $batch_id) {
        $join->on('admissions.student_id', '=', 'attendances.student_id')
             ->where('attendances.date', $date)
             ->where('attendances.hour', $hour);
              })

     ->leftjoin ('papers', function($join){
        $join->on('attendances.programme_id','=','papers.id')
            ->where('attendances.programme_type','=',1);
     }) 

      ->leftjoin ('events', function($join){
        $join->on('attendances.programme_id','=','events.id')
            ->where('attendances.programme_type','=',2);

        
     })       
    ->where('admissions.batch_id', $batch_id)
    ->select('admissions.student_id', 'attendances.attendance',
        DB::raw("
            case
                when attendances.programme_type=1 then papers.name
                when attendances.programme_type=2 then events.name
                else null
            end as programme_name
        ")
    
    )
    ->get();
          
     return response()->json($students);
}



public function showProgrammeAttendance(Request $request){

     $request->validate([
         'programme_id'=>'required|exists:programmes,id',
            'date'        =>'required|date'
    ]);

    $programme_id=$request->query('programme_id');
    $date=$request->query('date');

     $results = DB::table('attendances')
        ->join('admissions','attendances.student_id','=','admissions.student_id')
        ->join('batches','admissions.batch_id','=','batches.id')
        ->where('attendances.date', $date)
        ->where('batches.programme_id', $programme_id)
        ->select('admissions.batch_id',
                DB::raw("sum(case when attendances.attendance=0 then 1 else 0 end) as absentees"),
                DB::raw("sum(case when attendances.attendance=1 then 1 else 0 end) as presents"),
                DB::raw("sum(case when attendances.attendance=2 then 1 else 0 end) as latecomers")
                )
         ->groupBy('admissions.batch_id')
        ->get();

    return response()->json($results);
}

}

