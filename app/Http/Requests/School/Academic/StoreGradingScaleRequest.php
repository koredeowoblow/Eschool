<?php

namespace App\Http\Requests\School\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradingScaleRequest extends FormRequest
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
            'grade_label' => 'required|string|max:5',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'remark' => 'nullable|string|max:255',
            'is_pass' => 'boolean',
            'session_id' => 'nullable|exists:sessions,id',
        ];
    }
}
