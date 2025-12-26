<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;

class FeePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['super_admin', 'school_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'fee_id' => 'required|exists:fees,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,card,cheque,other',
            'reference_number' => 'nullable|string|max:50|unique:fee_payments,reference_number',
            'payment_date' => 'nullable|date|before_or_equal:today',
            // Do not allow processed_by from client, use auth()->id() instead
        ];
    }

    /**
     * Sanitize and add auth id.
     */
    protected function passedValidation(): void
    {
        $this->merge(['processed_by' => \Illuminate\Support\Facades\Auth::id()]);
    }
}
