<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;

class BulkFeeTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fee_types' => 'required|array',
            'fee_types.*.name' => 'required|string|max:255',
            'fee_types.*.description' => 'nullable|string',
            'fee_types.*.amount' => 'required|numeric|min:0',
        ];
    }
}
