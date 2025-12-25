<?php

namespace App\Http\Requests\Assignment\Create;

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
            'assessment_id' => 'required|integer|exists:assessments,id',
            'student_id' => 'required|integer|exists:students,id',
            'marks_obtained' => 'required|integer|min:0',
            'grade' => 'required|in:A,B,C,D,F',
            'remark' => 'nullable|string',
        ];
    }
}
