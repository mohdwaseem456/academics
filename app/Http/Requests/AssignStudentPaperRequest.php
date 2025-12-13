<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssignStudentPaperRequest extends FormRequest
{
    public function authorize(): bool
    {
        $facultyId = auth('api')->id();
        $paperId = $this->input('paper_id');

        return DB::table('paper_faculty')
            ->where('faculty_id', $facultyId)
            ->where('paper_id', $paperId)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'paper_id' => [
                'required',
                'integer',
                'exists:papers,id',
            ],
            'students' => [
                'required',
                'array',
                'min:1',
            ],
            'students.*' => [
                'required',
                'integer',
                'exists:students,student_id',
                Rule::unique('student_paper', 'student_id')->where(function ($query) {
                    return $query->where('paper_id', $this->paper_id);
                }),
            ],
        ];
    }
}
