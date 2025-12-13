<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignStudentPaperRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StudentPaperController extends Controller
{
    public function assign(AssignStudentPaperRequest $request): JsonResponse
    {
        $paperId  = $request->paper_id;
        $students = $request->students;

        DB::beginTransaction();

        try {
            $created = [];

            foreach ($students as $studentId) {
                // Skip if the student already has this paper assigned
                $exists = DB::table('student_paper')
                            ->where('student_id', $studentId)
                            ->where('paper_id', $paperId)
                            ->exists();

                if ($exists) continue;

                $id = DB::table('student_paper')->insertGetId([
                    'student_id' => $studentId,
                    'paper_id'   => $paperId,
                    'status'     => 1, // Assigned/Active
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $created[] = DB::table('student_paper')->find($id);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Student paper assignments created successfully.',
            //    'paper_id'=> $paperId,
              //  'students'=> $created,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            logger()->error('Student Paper Assignment failed: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'An error occurred during assignment. Please try again.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
