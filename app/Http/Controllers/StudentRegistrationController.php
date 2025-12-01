<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRegistrationRequest;
use App\Models\Programme;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB; // Import the DB Facade
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class StudentRegistrationController extends Controller
{
    /**
     * Handles the student signup request using the Query Builder.
     *
     * @param StudentRegistrationRequest $request
     * @return JsonResponse
     */
    public function signup(StudentRegistrationRequest $request): JsonResponse
    {
        // 1. Validation: Handled automatically by the StudentRegistrationRequest.

        // 2. Programme Lookup (Existence Check)
        $programme = Programme::where('name', $request->programme_name)->first();

        if (!$programme) {
            return response()->json([
                'message' => 'The requested programme was not found.',
                'errors' => ['programme_name' => ['The specified programme name does not exist.']]
            ], 422);
        }

        // 3. Create Pending Registration Record using Query Builder
        
            // Define constants for status clarity (optional but good practice)
            $STATUS_PENDING = 0; 
            
            // Use DB::table() for direct table interaction
                    $id = DB::table('student_registrations')->insertGetId([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_no' => $request->phone_no,
                'programme_name' => $request->programme_name,
                
                // ⚠️ NEW: Insert Email
                'email' => $request->email,
                
                // ⚠️ NEW: Hash and Insert Password
                'password' => Hash::make($request->password), 
                
                'status' => $STATUS_PENDING,
                'created_at' => now(), 
                'updated_at' => now(), 
]);
            // 4. API Response
            return response()->json([
                'message' => 'Application submitted successfully. Awaiting faculty review and approval.',
                'application_id' => $id
            ], 201);

  
    }
}