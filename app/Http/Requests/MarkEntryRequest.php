<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            
            'paper_assessment_id' =>'required|exists:paper_assessment,id',
            'marks'               =>'required|array|min:1',
            'marks.*.student_id'  =>'required|exists:students,student_id',
            'marks.*.mark'        =>'required|numeric|min:0'



        ];
    }
}
