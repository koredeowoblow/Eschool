<?php

namespace App\Http\Requests\Class;

use App\Http\Requests\BaseRequest;

class LessonNoteRequest extends BaseRequest
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
    protected function prepareForValidation()
    {
        if (!$this->has('teacher_id') && $this->user()->hasRole('Teacher')) {
            $profile = $this->user()->teacher()->first();
            if ($profile) {
                $this->merge(['teacher_id' => $profile->id]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'teacher_id' => 'required|integer|exists:teacher_profiles,id',
                'subject_id' => 'required|integer|exists:subjects,id',
                'class_id' => 'required|integer|exists:classes,id',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'teacher_id' => 'sometimes|integer|exists:teacher_profiles,id',
                'subject_id' => 'sometimes|integer|exists:subjects,id',
                'class_id' => 'sometimes|integer|exists:classes,id',
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|string',
            ];
        }

        return [];
    }
}
