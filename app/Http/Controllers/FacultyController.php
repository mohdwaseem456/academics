<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Carbon; 
use App\Services\StudentEnrollmentService; 

class FacultyController extends Controller
{
    private const STATUS_PENDING = 0; 
    private const STATUS_APPROVED = 1; 
    private const STATUS_REJECTED = 2; 

    public function approveStudent(int $id, StudentEnrollmentService $enrollmentService): JsonResponse
    {
        $registration = DB::table('student_registrations')->where('id', $id)->first();
        if (!$registration) {
            return response()->json(['message' => 'Registration application not found.'], 404);
        }

        $this->updateRegistrationStatus($id, self::STATUS_APPROVED);

        $enrollmentService->enrollStudent($registration);

        return response()->json([
            'message' => 'Registration approved successfully. Student enrollment process initiated.',
            'registration_id' => $id,
            'new_status' => self::STATUS_APPROVED,
        ], 200);
    }

    public function rejectStudent(int $id): JsonResponse
    {
        $registration = DB::table('student_registrations')->where('id', $id)->first();
        if (!$registration) {
            return response()->json(['message' => 'Registration application not found.'], 404);
        }

        $this->updateRegistrationStatus($id, self::STATUS_REJECTED);

        return response()->json([
            'message' => 'Registration rejected successfully.',
            'registration_id' => $id,
            'new_status' => self::STATUS_REJECTED,
        ], 200);
    }

    private function updateRegistrationStatus(int $id, int $newStatus): void
    {
        DB::table('student_registrations')
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => Carbon::now(),
            ]);
    }




    public function showRegistrations(Request $request): JsonResponse
    {
        // 1. Retrieve all student registrations using Query Builder
        $registrations = DB::table('student_registrations')
            ->select(
                'id as application_id',
                'first_name',
                'last_name',
                'programme_name' ,
                'phone_no',
                'created_at as submitted_on'
                
            )->where('status', self::STATUS_PENDING) // ⚠️ Filter for pending status (0)
            ->get();
           

        if ($registrations->isEmpty()) {
            return response()->json([
                'message' => 'There are no student registration applications at this time.'
            ], 200);
        }

        // 2. Successful Response - Returning the raw database results
        return response()->json([
            'message' => 'Student registrations retrieved successfully.',
            'pending applications' => $registrations->count(),
            'data' => $registrations, // ⚠️ Returning the raw database collection/array
        ], 200);
    }
}