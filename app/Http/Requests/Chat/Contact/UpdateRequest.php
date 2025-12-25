<?php

namespace App\Http\Requests\Chat\Contact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email',
            'subject' => 'sometimes|string|max:150',
            'message' => 'sometimes|string',
            'status'  => 'sometimes|string',
        ];
    }
}
