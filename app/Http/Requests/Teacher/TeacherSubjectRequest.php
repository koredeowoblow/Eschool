<?php

namespace App\Http\Requests\Teacher;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Auth;

class TeacherSubjectRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->hasRole('super_admin') || Auth::user()?->hasRole('school_admin');
    }

    public function rules(): array
    {
        return [
            'teacher_id' => 'required|exists:teacher_profiles,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
        ];
    }
}
