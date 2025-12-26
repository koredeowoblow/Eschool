<?php

namespace App\Http\Requests\Academic;

use App\Http\Requests\BaseRequest;

class AttendanceRequest extends BaseRequest
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
                'student_id' => 'required|integer|exists:students,id',
                'class_id' => 'required|integer|exists:class_rooms,id',
                'date' => 'required|date',
                'status' => 'required|string|in:present,absent,late,excused',
                'remarks' => 'nullable|string'
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'status' => 'sometimes|string|in:present,absent,late,excused',
                'remarks' => 'nullable|string'
            ];
        }

        return [];
    }
}
