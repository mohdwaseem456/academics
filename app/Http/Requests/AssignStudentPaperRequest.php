<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssignStudentPaperRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * This checks if the authenticated faculty is assigned to the requested paper.
     */
    public function authorize(): bool
    {
        // 1. Get the authenticated faculty's user ID
        $facultyId = auth('api')->id();
        
        // 2. Get the paper_id from the request
        $paperId = $this->input('paper_id');

        // Note: The 'paper_id' existence check is handled by the rules() method ('exists:papers,id').
       /* 
        if (empty($paperId)) {
            // If paper_id is missing, let the rules() method return the validation error.
            return false; 
        }*/

        // 3. Check if this faculty is assigned to this paper in the paper_faculties table
        $isAuthorized = DB::table('paper_faculty')
            ->where('faculty_id', $facultyId)
            ->where('paper_id', $paperId)
            ->exists();

        // If not authorized, Laravel will automatically return a 403 Forbidden response.
        return $isAuthorized;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'student_id' => [
                'required', 
                'integer', 
                // Check if the student_id exists in the students table
                'exists:students,student_id',
                // Ensure student_id and paper_id pair is unique in student_papers table
                Rule::unique('student_paper')->where(function ($query) {
                    return $query->where('paper_id', $this->paper_id);
                }),
            ],
            'paper_id' => [
                'required', 
                'integer', 
                // Check if the paper_id exists in the papers table
                'exists:papers,id',
            ],
        ];
    }
}