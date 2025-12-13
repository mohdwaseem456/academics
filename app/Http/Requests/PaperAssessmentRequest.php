<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaperAssessmentRequest extends FormRequest
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
            'paper_id' =>'required|exists:papers,id',
            'assessment_type_id'=>'required|exists:assessment_types,id',
            'max_mark' =>'required|integer|in:25,50,75,100',
            'scale_id' =>'required|exists:scales,id',
        ];
    }
}
