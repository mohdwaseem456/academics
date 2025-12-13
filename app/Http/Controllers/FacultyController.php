<?php

namespace App\Http\Controllers;

//use App\Jobs\SendRegisterStatusMailJob;
use App\Events\StudentRegistrationProcessed;
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

    /** @var StudentEnrollmentService */
    private $enrollmentService;   // <---- DECLARED HERE

    public function __construct(StudentEnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;  
    }

    public function approveStudent(int $id): JsonResponse
    {
        $registration = DB::table('student_registrations')->where('id', $id)->first();
        if (!$registration) {
            return response()->json(['message' => 'Registration application not found.'], 404);
        }

        $this->updateRegistrationStatus($id, self::STATUS_APPROVED);

        // use service from constructor
        $this->enrollmentService->enrollStudent($registration);

      //  dispatch(new SendRegisterStatusMailJob((object)$registration, 'approved'));
      event(new \App\Events\StudentRegistrationProcessed((object)$registration, 'approved'));


        return response()->json([
            'message' => 'Registration approved successfully. Student enrollment initiated.',
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

      //  dispatch(new SendRegisterStatusMailJob((object)$registration, 'rejected'));
      event(new \App\Events\StudentRegistrationProcessed((object)$registration, 'approved'));

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
        $registrations = DB::table('student_registrations')
            ->select(
                'id as application_id',
                'first_name',
                'last_name',
                'programme_name',
                'phone_no',
                'created_at as submitted_on'
            )
            ->where('status', self::STATUS_PENDING)
            ->get();

        if ($registrations->isEmpty()) {
            return response()->json([
                'message' => 'There are no pending applications.'
            ], 200);
        }

        return response()->json([
            'message' => 'Student registrations retrieved successfully.',
            'pending_applications' => $registrations->count(),
            'data' => $registrations,
        ], 200);
    }
}
