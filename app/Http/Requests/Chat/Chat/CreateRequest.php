<?php

namespace App\Http\Requests\Chat\Chat;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sender_id' => 'nullable|uuid|exists:users,id',
            'receiver_id' => 'required|uuid|exists:users,id',
            'message' => 'required|string',
            'is_read' => 'nullable|boolean',
            'read_at' => 'nullable|date',
        ];
    }
}
