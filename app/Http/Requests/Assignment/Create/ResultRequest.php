<?php

namespace App\Http\Requests\Assignment\Create;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['teacher', 'school_admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $schoolId = auth()->user()->school_id;

        return [
            'assessment_id' => ['required', 'integer', Rule::exists('assessments', 'id')->where('school_id', $schoolId)],
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')->where('school_id', $schoolId)],
            'marks_obtained' => 'required|integer|min:0',
            'grade' => 'required|in:A,B,C,D,F',
            'remark' => 'nullable|string',
        ];
    }
}
