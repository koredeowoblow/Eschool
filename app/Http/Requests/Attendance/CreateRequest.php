<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
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
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')->where('school_id', $schoolId)],
            'class_id'   => ['required', 'integer', Rule::exists('classes', 'id')->where('school_id', $schoolId)],
            'date'       => 'required|date',
            'status'     => 'required|in:present,absent,late,excused',
        ];
    }
}
