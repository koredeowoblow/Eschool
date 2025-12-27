<?php

namespace App\Http\Requests\InvoiceItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceItemRequest extends FormRequest
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
            'fee_type_id' => 'sometimes|integer|exists:fee_types,id',
            'amount' => 'sometimes|numeric|min:0',
        ];
    }
}
