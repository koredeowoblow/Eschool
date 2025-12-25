<?php

namespace App\Http\Requests\Assignment\Create;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['teacher', 'school_admin', 'super_admin']);
    }

    public function rules(): array
    {
        $user = auth()->user();

        $rules = [
            'class_id' => 'required|integer|exists:classes,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'type' => 'required|in:exam,test,project',
            'total_marks' => 'required|integer|min:1',
            'date' => 'required|date',
        ];

        // If teacher, don't allow teacher_id in the request
        if ($user->hasRole('teacher')) {
            // teacher_id will be injected later
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

        if ($user->hasRole('teacher')) {
            $teacherId = $user->teacherProfile->id ?? null;
            if (!$teacherId) {
                abort(403, 'Teacher profile not found.');
            }
            $data['teacher_id'] = $teacherId;
        }

        return $data;
    }
}
