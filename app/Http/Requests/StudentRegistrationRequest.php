<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRegistrationRequest extends FormRequest
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
           
        'first_name' => ['required', 'string', 'max:50'],
        'last_name' => ['required', 'string', 'max:50'],
        'phone_no' => ['required', 'string', 'unique:student_registrations', 'max:15'],
        'programme_name' => ['required', 'string', 'max:100'],
        
        // ⚠️ NEW: Add Validation for Email and Password
        'email' => ['required', 'string', 'email', 'max:150', 'unique:student_registrations'],
        'password' => ['required', 'string'], // 'confirmed' checks for password_confirmation field
    ];
       
    }}
