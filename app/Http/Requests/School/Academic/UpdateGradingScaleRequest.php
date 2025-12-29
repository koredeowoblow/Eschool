<?php

namespace App\Http\Requests\School\Academic;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradingScaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'grade_label' => 'sometimes|string|max:5',
            'min_score' => 'sometimes|numeric|min:0|max:100',
            'max_score' => 'sometimes|numeric|min:0|max:100|gte:min_score',
            'remark' => 'nullable|string|max:255',
            'is_pass' => 'boolean',
            'session_id' => 'nullable',
        ];
    }
}
