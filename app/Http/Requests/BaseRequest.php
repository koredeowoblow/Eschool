<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Default to true if authenticated, let specific requests handle fine-grained auth
        return \Illuminate\Support\Facades\Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = \App\Helpers\ResponseHelper::error(
            'Validation failed',
            422,
            $validator->errors()
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
