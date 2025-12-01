<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentEnrollmentService
{
    public function enrollStudent(object $registration): array
    {
        // 1. Create a User record in the 'users' table
        $userId = DB::table('users')->insertGetId([
            'email' => $registration->email,
            'password' =>$registration->password , 
            'user_type' => 1,
            'status' => "Active", 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // 2. Create the corresponding Student profile record
        DB::table('students')->insert([
            'student_id' => $userId,
            'first_name' => $registration->first_name,
            'last_name' => $registration->last_name,
            'phone_number' => $registration->phone_no,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'user_id' => $userId,
            'status' => 'enrolled',
            'details' => 'User and Student profile records created.'
        ];
    }
}