<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateStudentRequest extends FormRequest
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
        $studentId = $this->route('student') ?? $this->route('id');

        return [
            'admission_number' => 'sometimes|string|unique:students,admission_number,' . $studentId,
            'admission_date' => 'sometimes|date',
            'status' => 'sometimes|in:active,graduated,withdrawn',
            'class_id' => 'sometimes|exists:classes,id',
            'section_id' => 'sometimes|exists:sections,id',

            'school_session_id' => 'sometimes|exists:school_sessions,id',
        ];
    }
}
