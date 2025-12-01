<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignStudentPaperRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentPaperController extends Controller
{
    /**
     * Assigns a paper (course) to a student.
     * The request is already validated and authorized by AssignStudentPaperRequest.
     */
    public function assign(AssignStudentPaperRequest $request)
    {
        // Data is safe, validated, and authorized at this point.
        $validatedData = $request->validated();
        
        // Use a database transaction for safe insertion
        DB::beginTransaction();

        try {
            // Insert the record into the student_papers table
            $assignment = DB::table('student_paper')->insertGetId([
                'student_id' => $validatedData['student_id'],
                'paper_id' => $validatedData['paper_id'],
                'status' => 1, // Default status: 1 (e.g., Assigned/Active)
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();

            // Fetch the inserted record for a detailed response (optional, but good practice)
            $newAssignment = DB::table('student_paper')->find($assignment);
            
            // Return a structured JSON response with a 201 Created status
            return response()->json([
                'message' => 'Student paper assignment created successfully.',
                'data' => $newAssignment
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            // Log the error for debugging
            logger()->error('Student Paper Assignment failed: ' . $e->getMessage());

            // Return a 500 status for database error
            return response()->json([
                'message' => 'An error occurred during assignment. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}