<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AccountLinkingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('account-linking.initiate')) {
            return [
                'school_id' => 'required|exists:schools,id',
                'email' => 'required|email'
            ];
        }

        if ($this->routeIs('account-linking.verify')) {
            return [
                'token' => 'required|string',
                'otp' => 'required|string|size:6'
            ];
        }

        return [];
    }
}
