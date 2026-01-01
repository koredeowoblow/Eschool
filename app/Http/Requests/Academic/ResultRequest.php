<?php

namespace App\Http\Requests\Academic;

use App\Http\Requests\BaseRequest;

class ResultRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('School Admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('Teacher') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('Student');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'assessment_id' => 'required|integer|exists:assessments,id',
                'student_id' => 'required|integer|exists:students,id',
                'marks_obtained' => 'required|numeric|min:0',
                'grade' => 'nullable|string|max:5',
                'comments' => 'nullable|string',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'marks_obtained' => 'sometimes|numeric|min:0',
                'grade' => 'sometimes|string|max:5',
                'comments' => 'nullable|string',
            ];
        }

        return [];
    }
}
