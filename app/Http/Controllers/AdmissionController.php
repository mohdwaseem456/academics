<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Http\Requests\AdmissionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class AdmissionController extends Controller
{
    public function giveAdmission(AdmissionRequest $request): JsonResponse
    {
        $batchId  = $request->batch_id;
        $students = $request->students;

        DB::beginTransaction();

        try {
            $created = [];

            foreach ($students as $studentId) {
                $exists = Admission::where('student_id', $studentId)
                                   ->where('batch_id', $batchId)
                                   ->exists();
                if ($exists) continue;

                $last = Admission::max('admission_number');
                $admissionNumber = $last ? $last + 1 : 100;

                $admission = Admission::create([
                    'student_id'       => $studentId,
                    'batch_id'         => $batchId,
                    'admission_number' => $admissionNumber,
                    'roll_number'      => null,
                ]);

                $created[] = $admission;
            }

            // Step 1: Set all roll_numbers in this batch to NULL
            Admission::where('batch_id', $batchId)->update(['roll_number' => null]);

            // Step 2: Fetch all admissions in this batch with students
            $batchAdmissions = Admission::with('student')
                                        ->where('batch_id', $batchId)
                                        ->get();

            // Step 3: Sort alphabetically in memory
            $sorted = $batchAdmissions->sortBy(function ($a) {
                $first = $a->student->first_name ?? '';
                $last  = $a->student->last_name ?? '';
                return strtolower($first . ' ' . $last);
            })->values();

            // Step 4: Assign new roll_numbers
            foreach ($sorted as $i => $adm) {
                $adm->roll_number = $i + 1;
                $adm->save();
            }

            // Step 5: Prepare response for newly created students
            $createdData = collect($created)->map(function ($adm) {
                $admFresh = Admission::with('student')->find($adm->id);
                return [
                    'id'   => $admFresh->student->student_id,
                    'name' => $admFresh->student->first_name . ' ' . ($admFresh->student->last_name ?? ''),
                    'roll' => $admFresh->roll_number,
                ];
            });

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Admissions saved and roll numbers recalculated.',
                'batch_id' => $batchId,
                'students' => $createdData,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
