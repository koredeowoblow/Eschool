<?php

namespace App\Http\Requests\Class;

use App\Http\Requests\BaseRequest;

class SessionRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('School Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'name' => 'required|string|max:150',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'status' => 'required|in:active,ended',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'name' => 'sometimes|string|max:150',
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date',
                'status' => 'sometimes|in:active,ended',
            ];
        }

        return [];
    }
}
