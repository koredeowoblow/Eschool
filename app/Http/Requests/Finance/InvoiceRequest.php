<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'fee_type_ids' => 'required|array',
            'fee_type_ids.*' => 'exists:fee_types,id',
            'due_date' => 'required|date',
            'notes' => 'nullable|string'
        ];
    }
}
