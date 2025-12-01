<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdmissionRequest extends FormRequest
{
    public function authorize()
    {
        // Allow teacher middleware to handle authorization
        return true;
    }

    public function rules()
    {
        return [
            'student_id'        => 'required|exists:students,student_id',
            'batch_id'          => 'required|exists:batches,id',
            'admission_number'  => 'required|unique:admissions,admission_number',
            'roll_number'       => 'required|unique:admissions,roll_number',
        ];
    }

}
