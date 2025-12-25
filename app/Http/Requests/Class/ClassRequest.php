<?php

namespace App\Http\Requests\Class;

use App\Http\Requests\BaseRequest;

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
            return [
                'name' => 'required|string|max:255',
                'section_id' => 'nullable|integer|exists:sections,id',
                'session_id' => 'required|integer|exists:school_sessions,id',
                'term_id' => 'required|integer|exists:terms,id',
                'class_teacher_id' => 'required|integer|exists:teacher_profiles,id',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'name' => 'sometimes|string|max:255',
                'section_id' => 'nullable|integer|exists:sections,id',
                'session_id' => 'sometimes|integer|exists:school_sessions,id',
                'term_id' => 'sometimes|integer|exists:terms,id',
                'class_teacher_id' => 'sometimes|integer|exists:teacher_profiles,id',
            ];
        }

        return [];
    }
}
