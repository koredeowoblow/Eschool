<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class TeacherRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('School Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('teacher'); // Get the 'teacher' parameter from the route if it exists (for update)

        if ($this->isMethod('POST')) {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'employee_number' => 'required|string|max:50|unique:teacher_profiles,employee_number',
                'hire_date' => 'nullable|date',
                'qualification' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'school_id' => \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') ? 'required|uuid|exists:schools,id' : 'nullable',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $this->user_id, // Note: user_id is passed in the request or fetched from model
                'status' => 'sometimes|in:0,1',
                'employee_number' => 'sometimes|string|max:50|unique:teacher_profiles,employee_number,' . $id,
                'hire_date' => 'nullable|date',
                'qualification' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
            ];
        }

        return [];
    }
}
