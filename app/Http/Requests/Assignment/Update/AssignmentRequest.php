<?php

namespace App\Http\Requests\Assignment\Update;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['teacher', 'school_admin', 'super_admin']);
    }

    public function rules(): array
    {
        $user = auth()->user();

        $rules = [
            'class_id' => 'nullable|integer|exists:classes,id',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ];

        // If teacher, don't allow teacher_id in the request
        if ($user->hasRole('Teacher')) {

        } else {
            // super_admin / school_admin must pass teacher_id
            $rules['teacher_id'] = 'required|integer|exists:teacher_profiles,id';
        }

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        $user = auth()->user();

        if ($user->hasRole('Teacher')) {
            $teacherId = $user->teacherProfile->id ?? null;
            if (!$teacherId) {
                abort(403, 'Teacher profile not found.');
            }
            $data['teacher_id'] = $teacherId;
        }

        return $data;
    }
}
