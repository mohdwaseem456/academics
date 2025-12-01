<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Http\Requests\AdmissionRequest;

class AdmissionController extends Controller
{
    public function giveAdmission(AdmissionRequest $request)
    {
        // Already validated by AdmissionRequest...

        // Check if the student already has admission in this batch
        $exists = Admission::where('student_id', $request->student_id)
                            ->where('batch_id', $request->batch_id)
                            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'This student is already admitted in this batch.'
            ], 409);
        }

        // Create the admission record
        $admission = Admission::create([
            'student_id'       => $request->student_id,
            'batch_id'         => $request->batch_id,
            'admission_number' => $request->admission_number,
            'roll_number'      => $request->roll_number,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Admission successfully created.',
            'data'    => $admission
        ]);
    }
}
