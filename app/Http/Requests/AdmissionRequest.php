<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdmissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

          
            'batch_id'   => 'required|exists:batches,id',
            'students'  => 'required|array|min:1',
            'students.*' => 'required|integer|exists:students,student_id',
        ];
    }
}
