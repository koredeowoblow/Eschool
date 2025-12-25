<?php

namespace App\Http\Requests\Assignment\Create;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['teacher', 'school_admin', 'super_admin', 'student']);
    }

    public function rules(): array
    {
        $user = auth()->user();

        $rules = [
            'assignment_id' => 'required|integer|exists:assignments,id',
            'answer' => 'nullable|string',
            'file_path' => 'nullable|string',
            'submitted_at' => 'nullable|date',
            'score' => 'nullable|integer|min:0',
            'feedback' => 'nullable|string',
        ];

        // If student, student_id is auto-filled. If not student, required.
        if (!$user->hasRole('student')) {
            // super_admin / school_admin / teacher must pass student_id
            // Ensure it checks 'students' table, not teacher_profiles
            $rules['student_id'] = 'required|integer|exists:students,id';
        }

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        $user = auth()->user();

        if ($user->hasRole('student')) {
            $student = $user->student->first();
            if (!$student) {
                abort(403, 'Student profile not found.');
            }
            // Correctly map the Student ID, not the User ID
            $data['student_id'] = $student->id;
        }

        return $data;
    }
}
