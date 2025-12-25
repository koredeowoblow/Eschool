<?php

namespace App\Http\Requests\Assignment\Update;

use Illuminate\Foundation\Http\FormRequest;

class ResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'marks_obtained' => 'sometimes|integer|min:0',
            'grade' => 'sometimes|in:A,B,C,D,F',
            'remark' => 'nullable|string',
        ];
    }
}
