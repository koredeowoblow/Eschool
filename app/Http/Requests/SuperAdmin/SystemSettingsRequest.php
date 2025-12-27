<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class SystemSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Assuming dynamic settings, but typically we want to allow most fields
            // Or define specific keys.
            'site_name' => 'sometimes|string|max:255',
            'contact_email' => 'sometimes|email',
            // ... more specific rules if known ...
        ];
    }
}
