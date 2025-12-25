<?php

namespace App\Http\Requests\Chat\Chat;

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
          'message' => 'sometimes|string',
            'is_read' => 'sometimes|boolean',
            'read_at' => 'nullable|date',
        ];
    }
}
