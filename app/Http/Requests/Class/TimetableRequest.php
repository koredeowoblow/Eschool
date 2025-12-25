<?php

namespace App\Http\Requests\Class;

use App\Http\Requests\BaseRequest;

class TimetableRequest extends BaseRequest
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
                'class_id' => 'required|integer|exists:classes,id',
                'subject_id' => 'required|integer|exists:subjects,id',
                'teacher_id' => 'required|integer|exists:teacher_profiles,id',
                'day_of_week' => 'required|in:Mon,Tue,Wed,Thu,Fri,Sat',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'room' => 'nullable|string|max:100',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'class_id' => 'sometimes|integer|exists:classes,id',
                'subject_id' => 'sometimes|integer|exists:subjects,id',
                'teacher_id' => 'sometimes|integer|exists:teacher_profiles,id',
                'day_of_week' => 'sometimes|in:Mon,Tue,Wed,Thu,Fri,Sat',
                'start_time' => 'sometimes|date_format:H:i',
                'end_time' => 'sometimes|date_format:H:i|after:start_time',
                'room' => 'nullable|string|max:100',
            ];
        }

        return [];
    }
}
