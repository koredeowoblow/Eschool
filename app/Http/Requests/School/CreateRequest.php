<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:schools,slug',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:schools,email',
            'state' => 'required|string|max:100',
            'area' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'plan' => 'required|string|in:basic,standard,premium',
            'status' => 'required|string|in:active,pending,suspended',
        ];
    }
}
