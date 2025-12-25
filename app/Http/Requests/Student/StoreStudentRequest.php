<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && $this->user()->hasAnyRole(['super_admin', 'school_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // User Fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',

            // Student Fields
            // 'user_id' => 'required|exists:users,id', // Removed: User is created inline
            'admission_date' => 'nullable|date',
            'status' => 'nullable|in:active,graduated,withdrawn',
            'class_id' => 'required|exists:classes,id',
            // 'section_id' => 'required|exists:sections,id', // Optional if inferred
            // 'school_session_id' => 'required|exists:school_sessions,id', // Optional if inferred
        ];
    }
}
