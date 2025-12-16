<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaperAssessmentRequest;
use App\Http\Requests\MarkEntryRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Exports\StudentMarksExport;
use Maatwebsite\Excel\Facades\Excel;


class PaperController extends Controller
{

    public function assignAssessment(PaperAssessmentRequest $request)
    {

        $paper_id = $request->paper_id;
        if (!$this->checkFacultyPaperAssignment($paper_id)) {
            return response()->json(['message' => 'Unauthorized']);
        }

        $exists = DB::table('paper_assessment')
            ->where('paper_id', $request->paper_id)
            ->where('assessment_type_id', $request->assessment_type_id)
            ->first();
        if ($exists) {
            return response()->json(['message' => 'This assessment already created for this paper']);
        }

        try {
            $inserted = DB::table('paper_assessment')->insert([

                'paper_id' => $request->paper_id,
                'assessment_type_id' => $request->assessment_type_id,
                'max_mark' => $request->max_mark,
                'scale_id' => $request->scale_id,
            ]);

            if ($inserted) {
                return response()->json(['message' => 'Successfully created assessment for this paper']);
            }
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to create assessment for this paper']);
        }
    }
    /////////////////////////////////

    public function showAssessments(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'paper_id' => 'required|exists:papers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "validation failed",
                "errors"  => $validator->errors()
            ]);
        }

        $paper_id = $request->query('paper_id');

        $data = DB::table('paper_assessment')
            ->where('paper_id', $paper_id)
            ->select('id as paper_assessment_id', 'assessment_type_id', 'max_mark', 'scale_id')
            ->get();

        return response()->json([
            'assessments' => $data
        ]);
    }
    ///////////////////////////////////////////

    public function deleteAssessment(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'paper_assessment_id' => 'required|exists:paper_assessment,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "validation failed",
                "errors"  => $validator->errors()
            ]);
        }
        $paper_assessment_id = $request->query('paper_assessment_id');

        $markentered = DB::table('student_assessment_mark')
            ->where('paper_assessment_id', $paper_assessment_id)
            ->first();
        if ($markentered) {
            return response()->json(["Already entered mark for this assessment"]);
        }

        DB::table('paper_assessment')
            ->where('id', $paper_assessment_id)
            ->delete();

        return response()->json([
            'Deleted successfully'
        ]);
    }
    /////////////////////////////////////

    public function updateAssessment(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'paper_assessment_id'    => 'required|exists:paper_assessment,id',
            'assessment_type_id'     => 'nullable|exists:assessment_types,id|required_without_all:max_mark,scale_id',
            'max_mark'               => 'nullable|numeric|in:25,50,75,100|required_without_all:assessment_type_id,scale_id',
            'scale_id'               => 'nullable|exists:scales,id|required_without_all:assessment_type_id,max_mark',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "validation failed",
                "errors"  => $validator->errors()
            ]);
        }

        $paper_assessment_id = $request->query('paper_assessment_id');

        if ($request->has('max_mark')) {
            $data['max_mark'] = $request->max_mark;
        }
        if ($request->has('scale_id')) {
            $data['scale_id'] = $request->scale_id;
        }
        if ($request->has('assessment_type_id')) {
            $data['assessment_type_id'] = $request->assessment_type_id;
        }

        DB::table('paper_assessment')
            ->where('id', $paper_assessment_id)
            ->update($data);

        return response()->json([
            'updated successfully',
        ]);
    }
    ///////////////////////////////////

    public function markEntry(MarkEntryRequest $request)
    {
        $paper_assessment_id = $request->paper_assessment_id;
        $marks = $request->marks;

        $row = DB::table('paper_assessment')
            ->where('id', $paper_assessment_id)
            ->first();

        if (!$this->checkFacultyPaperAssignment($row->paper_id)) {
            return response()->json(['message' => 'Unauthorized']);
        }

        $ranges = DB::table('scale_ranges')
            ->where('scale_id', $row->scale_id)
            ->get();

        DB::beginTransaction();
        try {
            $warnings = [];
            $ignored = [];
            $updated = [];

            foreach ($marks as $m) {

                $enrolledpaper = DB::table('student_paper')
                    ->where('student_id', $m['student_id'])
                    ->where('paper_id', $row->paper_id)
                    ->exists();
                if (!$enrolledpaper) {
                    $ignored[] = [
                        'student_id' => $m['student_id'],
                        'message'    => "This student hadn't enrolled this paper"
                    ];
                    continue;
                }

                $percentage =  $m['mark'] / $row->max_mark * 100;

                $range = $ranges
                    ->where('percentage_from', '<=', $percentage)
                    ->where('percentage_to', '>', $percentage)
                    ->first();


                $enteredalready = DB::table('student_assessment_mark')
                    ->where('student_id', $m['student_id'])
                    ->where('paper_assessment_id', $paper_assessment_id)
                    ->first();
                if ($enteredalready) {
                    $change['mark'] = $m['mark'];

                    if ($range) {
                        $change['grade'] = $range->grade;
                        $change['gradepoint'] = $range->gradepoint;
                    } else {
                        $change['grade'] = NULL;
                        $change['gradepoint'] = NULL;
                        $warnings[] = "Grade entry failure: Percentage {$percentage} acquired by student with id {$m['student_id']} not included in any ranges";
                    }

                    DB::table('student_assessment_mark')
                        ->where('id', $enteredalready->id)
                        ->update($change);

                    $updated[] = [
                        'student_id' => $m['student_id'],
                    ];
                    continue;
                }
                if (!$range) {
                    $warnings[] = "Grade entry failure: Percentage {$percentage} acquired by student with id {$m['student_id']} not included in any ranges";

                    DB::table('student_assessment_mark')->insert([
                        'paper_assessment_id' => $paper_assessment_id,
                        'student_id'         => $m['student_id'],
                        'mark'               => $m['mark'],
                    ]);

                    continue;
                }

                DB::table('student_assessment_mark')->insert([
                    'paper_assessment_id' => $paper_assessment_id,
                    'student_id'         => $m['student_id'],
                    'mark'               => $m['mark'],
                    'grade'              => $range->grade,
                    'gradepoint'         => $range->gradepoint
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message'  => "Failed to insert",
            ]);
        }
        return response()->json([
            'message'  => "Mark insertion completed",
            'ignored'  => $ignored,
            'updated'  => $updated,
            'warnings' => $warnings
        ]);
    }

    /////////////////////////////////////////////// 

    public function showMarklist(Request $request)
    {

        $validator = Validator::make($request->query(), [
            'paper_assessment_id' => 'required|exists:paper_assessment,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "validation failed",
                "errors"  => $validator->errors()
            ]);
        }

        $paper_assessment_id = $request->query('paper_assessment_id');

        $data = DB::table('student_assessment_mark')
            ->where('paper_assessment_id', $paper_assessment_id)
            ->select('student_id', 'mark', 'grade', 'gradepoint')
            ->get();

        return response()->json([
            'marks' => $data
        ]);
    }

    /////////////////////////////////////////////

    public function markFinalise(Request $request)
    {

        $request->validate([
            'paper_id' => 'required|exists:papers,id',
        ]);


        $paper_id = $request->paper_id;
        $row_paper = DB::table('papers')
            ->where('id', $paper_id)
            ->first();

        $max_assessments_mark = DB::table('paper_assessment')
            ->where('paper_id', $paper_id)
            ->sum('max_mark');

        if (!$this->checkFacultyPaperAssignment($paper_id)) {
            return response()->json(['message' => 'Unauthorized']);
        }

        $updated = [];
        $warnings = [];

        $data = DB::table('student_paper as sp')
            ->leftjoin('paper_assessment as pa', 'sp.paper_id', '=', 'pa.paper_id')
            ->leftjoin('student_assessment_mark as sam', function ($join) {
                $join->on('sam.paper_assessment_id', '=', 'pa.id')
                    ->on('sp.student_id', '=', 'sam.student_id');
            })
            ->where("pa.paper_id", $paper_id)
            ->selectRaw('sp.student_id, COALESCE(SUM(sam.mark), 0) as total')
            ->groupby('sp.student_id')
            ->get();

        $ranges = DB::table('scale_ranges')
            ->where('scale_id', $row_paper->scale_id)
            ->get();;
        foreach ($data as $d) {
            $total = ($d->total) * $row_paper->max_mark / $max_assessments_mark;
            $percentage =  $total / $row_paper->max_mark * 100;

            $range = $ranges
                ->where('percentage_from', '<=', $percentage)
                ->where('percentage_to', '>', $percentage)
                ->first();

            $enteredalready = DB::table('student_paper_mark')
                ->where('student_id', $d->student_id,)
                ->where('paper_id', $paper_id)
                ->first();
            if ($enteredalready) {
                $change['mark'] = $total;

                if ($range) {
                    $change['grade'] = $range->grade;
                    $change['gradepoint'] = $range->gradepoint;
                } else {
                    $change['grade'] = NULL;
                    $change['gradepoint'] = NULL;
                    $warnings[] = "Grade entry failure: Percentage {$percentage} acquired by student with id {$d->student_id} not included in any ranges";
                }

                DB::table('student_paper_mark')
                    ->where('id', $enteredalready->id)
                    ->update($change);

                $updated[] = [
                    'student_id' => $d->student_id,
                ];
                continue;
            }
            if (!$range) {
                $warnings[] = "Grade entry failure: Percentage {$percentage} acquired by student with id {$d->student_id} not included in any ranges";

                DB::table('student_paper_mark')->insert([
                    'student_id' => $d->student_id,
                    'paper_id'   => $paper_id,
                    'mark'       => $total,
                ]);

                continue;
            }

            DB::table('student_paper_mark')->insert([
                'student_id' => $d->student_id,
                'paper_id'   => $paper_id,
                'mark'       => $total,
                'grade'      => $range->grade,
                'gradepoint' => $range->gradepoint

            ]);
        }

        return response()->json([
            'message' => 'Mark successfully finalised',
            'updated' => $updated,
            'warnings' => $warnings
        ]);
    }

    /////////////////////////////

    public function checkfacultypaperassignment($paper_id)
    {
        $faculty_Id = auth('api')->id();
        return DB::table('paper_faculty')
            ->where('paper_id', $paper_id)
            ->where('faculty_id', $faculty_Id)
            ->exists();
    }
    ///////////////////////////////

    public function markExport(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'paper_id' => 'required|exists:papers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $paper_id = $request->query('paper_id');
        $paper_name = DB::table('papers')
            ->where('id', $paper_id)
            ->value('name');

        $assessments = DB::table('paper_assessment as pa')
            ->join('assessment_types as at', 'pa.assessment_type_id', '=', 'at.id')
            ->where('pa.paper_id', $paper_id)
            ->pluck('at.name');

        $Selects = [];

        foreach ($assessments as $name) {
            $alias = strtolower(str_replace(' ', '_', $name));

            $Selects[] = DB::raw(
                "MAX(CASE WHEN at.name = '{$name}' THEN sam.mark END) AS `{$alias}`"
            );
        }

        $data = DB::table('student_paper as sp')
            ->leftJoin('admissions as a', 'sp.student_id', '=', 'a.student_id')
            ->leftJoin('students as s', 's.student_id', '=', 'a.student_id')
            ->leftJoin('paper_assessment as pa', 'pa.paper_id', '=', 'sp.paper_id')
            ->leftJoin('assessment_types as at', 'at.id', '=', 'pa.assessment_type_id')
            ->leftJoin('student_assessment_mark as sam', function ($join) {
                $join->on('sam.paper_assessment_id', '=', 'pa.id')
                    ->on('sam.student_id', '=', 'sp.student_id');
            })
            ->leftJoin('student_paper_mark as spm', function ($join) {
                $join->on('spm.paper_id', '=', 'sp.paper_id')
                    ->on('spm.student_id', '=', 'sp.student_id');
            })
            ->where('pa.paper_id', $paper_id)
            ->selectRaw("
                CONCAT(s.first_name, ' ', s.last_name) AS full_name,
                a.admission_number
               ")
            ->addSelect($Selects)
            ->addSelect(DB::raw('spm.mark AS total'))
            ->groupBy(
                'a.admission_number',
                's.first_name',
                's.last_name',
                'spm.mark'
            )
            ->get();

        return Excel::download(
            new StudentMarksExport($paper_name, $data),
            $paper_name . '_marks.xlsx'
        );
    }
}
