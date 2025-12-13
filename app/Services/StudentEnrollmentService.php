<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentEnrollmentService
{
    public function enrollStudent(object $registration): array
    {
        // 1. Insert into students table first
        $studentId = DB::table('students')->insertGetId([
            'first_name' => $registration->first_name,
            'last_name'  => $registration->last_name,
            'phone_number' => $registration->phone_no,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insert into users table using student ID
        $userId = DB::table('users')->insertGetId([
            'user_id'        => $studentId, // use same ID
            'email'     => $registration->email,
            'password'  => $registration->password, 
            'user_type' => 1,
            'status'    => "Active",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'user_id' => $userId,
            'status' => 'enrolled',
            'details' => 'Student created first, User created using same ID.'
        ];
    }
}
