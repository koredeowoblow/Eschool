<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:schools,slug,' . $this->route('id'),
            'address' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|string|email|max:255|unique:schools,email,' . $this->route('id'),
            'state' => 'sometimes|string|max:100',
            'area' => 'sometimes|string|max:100',
            'city' => 'sometimes|string|max:100',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'sometimes|string|max:255',
            'contact_person_phone' => 'sometimes|string|max:20',
            'status' => 'sometimes|string|in:active,pending,suspended',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
