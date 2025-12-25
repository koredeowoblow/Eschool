<?php

namespace App\Http\Requests\Class;

use App\Http\Requests\BaseRequest;

class EnrollmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('school_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'student_id' => 'required|integer|exists:students,id',
                'class_id' => 'required|integer|exists:classes,id',
                'session_id' => 'required|integer|exists:school_sessions,id',
                'term_id' => 'required|integer|exists:terms,id',
                'enrollment_date' => 'nullable|date',
                'status' => 'nullable|in:active,inactive',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'class_id' => 'sometimes|integer|exists:classes,id',
                'session_id' => 'sometimes|integer|exists:school_sessions,id',
                'term_id' => 'sometimes|integer|exists:terms,id',
                'enrollment_date' => 'nullable|date',
                'status' => 'nullable|in:active,inactive',
            ];
        }

        return [];
    }
}
