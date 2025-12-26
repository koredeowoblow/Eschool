<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Controller middleware handles roles
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'nullable|string|min:6',
                'role'     => ['required', Rule::in(['student', 'teacher', 'guardian', 'school_admin'])],

                // Student-specific fields
                'admission_number'   => 'required_if:role,student|string|max:50',
                'admission_date'     => 'required_if:role,student|date',
                'class_id'           => ['required_if:role,student', 'integer', Rule::exists('classes', 'id')->where('school_id', \Illuminate\Support\Facades\Auth::user()->school_id)],
                'school_session_id'  => ['required_if:role,student', 'integer', Rule::exists('school_sessions', 'id')->where('school_id', \Illuminate\Support\Facades\Auth::user()->school_id)],

                'term_id'            => ['required_if:role,student', 'integer', Rule::exists('terms', 'id')->where('school_id', \Illuminate\Support\Facades\Auth::user()->school_id)],

                // Teacher-specific fields
                'employee_number'    => 'required_if:role,teacher|string|max:50',
                'hire_date'          => 'required_if:role,teacher|date',
                'qualification'      => 'nullable|string|max:255',
                'department'         => 'nullable|string|max:255',
                'bio'                => 'nullable|string',
                'guardian_id'        => ['nullable', 'integer', Rule::exists('guardians', 'id')->where('school_id', \Illuminate\Support\Facades\Auth::user()->school_id)],

                // Guardian-specific fields
                'guardian'               => 'required_if:role,student|array',
                'guardian.name'          => 'required_if:role,student|string|max:255',
                'guardian.email'         => [
                    'required_if:role,student',
                    'email',
                    function ($attribute, $value, $fail) {
                        $existingUser = \App\Models\User::where('email', $value)->first();
                        if ($existingUser) {
                            // 1. Must be a guardian
                            if (!$existingUser->hasRole('Guardian')) {
                                $fail('This email is already registered to a ' . ($existingUser->getRoleNames()->first() ?? 'user') . ' account.');
                            }
                            // 2. Must belong to the same school
                            $currentUser = \Illuminate\Support\Facades\Auth::user();
                            if ($existingUser->school_id !== $currentUser->school_id) {
                                $fail('This guardian is registered in a different school.');
                            }
                        }
                    }
                ],
                'guardian.password'      => 'nullable|string|min:6',
                'guardian.relation'      => 'required_if:role,student|string|max:100',
                'guardian.occupation'    => 'required_if:role,student|string|max:255',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $userId = $this->route('user') ?? \Illuminate\Support\Facades\Auth::id();

            return [
                'name'    => 'sometimes|string|max:255',
                'email'   => ['sometimes', 'email', Rule::unique('users')->ignore($userId)],
                'password' => 'nullable|string|min:6',
                'phone'   => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'city'    => 'nullable|string|max:100',
                'state'   => 'nullable|string|max:100',
                'zip'     => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'guardian.required_if' => 'Guardian information is required when creating a student.',
            'guardian.*.required_if' => 'This guardian field is required.',
        ];
    }
}
