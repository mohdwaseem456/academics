<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceRequest extends FormRequest
{   
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Faculty members (or users authenticated via the 'api' guard)
        // should be authorized to mark attendance.
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Core Attendance Data
            'date' => ['required', 'date', 'before_or_equal:today'],
            'hour' => ['required', 'integer', 'min:1', 'max:8'], // Assuming 8 teaching hours max
            
            // programme_type: 1 = Paper, 2 = Event
            'programme_type' => ['required', 'integer', 'in:1,2'], 

            // Conditional validation for programme_id:
            // This field acts as EITHER the paper_id or the event_id
            'programme_id' => [
                'required', 
                'integer',
                // Check against 'papers' if type is 1 (Academic Paper)
                Rule::when($this->programme_type === 1, [
                    'exists:papers,id'
                ]),
                // Check against 'events' if type is 2 (Non-academic Event)
                Rule::when($this->programme_type === 2, [
                    'exists:events,id'
                ]),
            ],
            
            // Nested Students Array
            'students' => ['required', 'array', 'min:1'],
            
            // Validation for each student object within the array
            'students.*.student_id' => [
                'required', 
                'integer', 
                Rule::exists('students', 'student_id')
            ],
            
            'students.*.attendance' => [
                'required', 
                'integer', 
                'in:0,1' // 1 for Present, 0 for Absent
            ],
        ];
    }
}