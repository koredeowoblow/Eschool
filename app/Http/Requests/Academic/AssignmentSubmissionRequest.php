<?php

namespace App\Http\Requests\Academic;

use App\Http\Requests\BaseRequest;

class AssignmentSubmissionRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('school_admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('teacher') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('student');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'assignment_id' => 'required|integer|exists:assignments,id',
                'student_id' => 'required|integer|exists:students,id',
                'submission_text' => 'nullable|string',
                'file_path' => 'nullable|string',
                'status' => 'nullable|in:submitted,pending,late',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'submission_text' => 'sometimes|string',
                'file_path' => 'sometimes|string',
                'status' => 'sometimes|in:submitted,pending,late,graded',
            ];
        }

        return [];
    }
}
