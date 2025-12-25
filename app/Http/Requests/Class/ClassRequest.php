<?php

namespace App\Http\Requests\Class;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ClassRequest extends BaseRequest
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
            $schoolId = Auth::user()->school_id;

            return [
                'name' => 'required|string|max:255',
                'section_id' => ['nullable', 'integer', Rule::exists('sections', 'id')->where('school_id', $schoolId)],
                'session_id' => ['required', 'integer', Rule::exists('school_sessions', 'id')->where('school_id', $schoolId)],
                'term_id' => ['required', 'integer', Rule::exists('terms', 'id')->where('school_id', $schoolId)],
                'class_teacher_id' => ['required', 'integer', Rule::exists('teacher_profiles', 'id')->where('school_id', $schoolId)],
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $schoolId = Auth::user()->school_id;

            return [
                'name' => 'sometimes|string|max:255',
                'section_id' => ['nullable', 'integer', Rule::exists('sections', 'id')->where('school_id', $schoolId)],
                'session_id' => ['sometimes', 'integer', Rule::exists('school_sessions', 'id')->where('school_id', $schoolId)],
                'term_id' => ['sometimes', 'integer', Rule::exists('terms', 'id')->where('school_id', $schoolId)],
                'class_teacher_id' => ['sometimes', 'integer', Rule::exists('teacher_profiles', 'id')->where('school_id', $schoolId)],
            ];
        }

        return [];
    }
}
