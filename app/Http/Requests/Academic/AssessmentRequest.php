<?php

namespace App\Http\Requests\Academic;

use App\Http\Requests\BaseRequest;

class AssessmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('school_admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('teacher');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'class_id' => 'required|integer|exists:classes,id',
                'subject_id' => 'required|integer|exists:subjects,id',
                'teacher_id' => 'required|integer|exists:teacher_profiles,id',
                'title' => 'required|string|max:255',
                'type' => 'required|string|max:100',
                'max_points' => 'required|integer|min:0',
                'date' => 'required|date',
                'description' => 'nullable|string',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'class_id' => 'sometimes|integer|exists:classes,id',
                'subject_id' => 'sometimes|integer|exists:subjects,id',
                'teacher_id' => 'sometimes|integer|exists:teacher_profiles,id',
                'title' => 'sometimes|string|max:255',
                'type' => 'sometimes|string|max:100',
                'max_points' => 'sometimes|integer|min:0',
                'date' => 'sometimes|date',
                'description' => 'nullable|string',
            ];
        }

        return [];
    }
}
