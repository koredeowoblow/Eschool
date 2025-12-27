<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric',
            'no_of_students' => 'sometimes|integer',
            'no_of_teachers' => 'sometimes|integer',
            'no_of_guardians' => 'sometimes|integer',
            'no_of_staff' => 'sometimes|integer',
        ];
    }
}
