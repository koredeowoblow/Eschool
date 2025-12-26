<?php

namespace App\Http\Requests\Class;

use Illuminate\Foundation\Http\FormRequest;

class ClassIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['super_admin', 'school_admin', 'teacher']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'section_id' => 'nullable|exists:sections,id',
            'session_id' => 'nullable|exists:school_sessions,id',
            'term_id' => 'nullable|exists:terms,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
