<?php

namespace App\Http\Requests\Assignment\Update;

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

        // If teacher, don't allow teacher_id in the request
        if ($user->hasRole('Student_id')) {

        } else {
            // super_admin / school_admin must pass teacher_id
            $rules['student_id'] = 'required|integer|exists:teacher_profiles,id';
        }

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        $user = auth()->user();

        if ($user->hasRole(roles: 'student')) {
            $Student_id = $user->id ?? null;
            if (!$Student_id) {
                abort(403, message: 'Student profile not found.');
            }
            $data['student_id'] = $Student_id;
        }

        return $data;
    }
}
