<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'no_of_students' => 'required|integer',
            'no_of_teachers' => 'required|integer',
            'no_of_guardians' => 'required|integer',
            'no_of_staff' => 'required|integer',
        ];
    }
}
