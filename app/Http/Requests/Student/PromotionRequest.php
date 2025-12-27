<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:students,id',
            'to_class_id' => 'required|exists:classes,id',
            'to_session_id' => 'required|exists:school_sessions,id',
            'to_section_id' => 'nullable|exists:sections,id',
            'to_term_id' => 'nullable|exists:terms,id',
            'type' => 'required|in:promote,repeat',
        ];
    }
}
