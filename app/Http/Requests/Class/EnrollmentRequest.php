<?php

namespace App\Http\Requests\Class;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EnrollmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('School Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            $schoolId = Auth::user()->school_id;

            return [
                'student_id' => ['required', 'integer', Rule::exists('students', 'id')->where('school_id', $schoolId)],
                'class_id' => ['required', 'integer', Rule::exists('classes', 'id')->where('school_id', $schoolId)],
                'session_id' => ['required', 'integer', Rule::exists('school_sessions', 'id')->where('school_id', $schoolId)],
                'term_id' => ['required', 'integer', Rule::exists('terms', 'id')->where('school_id', $schoolId)],
                'enrollment_date' => 'nullable|date',
                'status' => 'nullable|in:active,inactive',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $schoolId = Auth::user()->school_id;

            return [
                'class_id' => ['sometimes', 'integer', Rule::exists('classes', 'id')->where('school_id', $schoolId)],
                'session_id' => ['sometimes', 'integer', Rule::exists('school_sessions', 'id')->where('school_id', $schoolId)],
                'term_id' => ['sometimes', 'integer', Rule::exists('terms', 'id')->where('school_id', $schoolId)],
                'enrollment_date' => 'nullable|date',
                'status' => 'nullable|in:active,inactive',
            ];
        }

        return [];
    }
}
