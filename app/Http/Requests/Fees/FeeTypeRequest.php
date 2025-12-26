<?php

namespace App\Http\Requests\Fees;

use App\Http\Requests\BaseRequest;

class FeeTypeRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('School Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'session_id' => 'nullable|integer|exists:school_sessions,id',
                'term_id' => 'nullable|integer|exists:terms,id',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'sometimes|numeric|min:0',
                'session_id' => 'sometimes|nullable|integer|exists:school_sessions,id',
                'term_id' => 'sometimes|nullable|integer|exists:terms,id',
            ];
        }

        return [];
    }
}
