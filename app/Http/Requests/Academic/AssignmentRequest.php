<?php

namespace App\Http\Requests\Academic;

use App\Http\Requests\BaseRequest;

class AssignmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('School Admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('Teacher');
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
                'description' => 'required|string',
                'due_date' => 'required|date|after:today',
                'max_score' => 'required|integer|min:0',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'class_id' => 'sometimes|integer|exists:classes,id',
                'subject_id' => 'sometimes|integer|exists:subjects,id',
                'teacher_id' => 'sometimes|integer|exists:teacher_profiles,id',
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'due_date' => 'sometimes|date|after:today',
                'max_score' => 'sometimes|integer|min:0',
            ];
        }

        return [];
    }
}
