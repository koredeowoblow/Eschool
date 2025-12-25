<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && $this->user()->hasAnyRole(['super_admin', 'school_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'student_id' => 'required|exists:students,id',
            'amount'     => 'required|numeric|min:0.01',
            'method'     => 'required|in:cash,bank_transfer,credit_card,cheque,online',
            'payment_date' => 'nullable|date',
            'transaction_ref' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'payment_proof' => 'nullable|string|max:255',
        ];
    }
}
