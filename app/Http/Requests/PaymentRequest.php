<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PaymentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'invoice_id' => 'required|string|exists:invoices,id',
            'student_id' => 'required|string|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'required|string|in:cash,bank_transfer,online',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'status' => 'sometimes|string|in:pending,completed,failed',
            'processed_by' => 'nullable|string|exists:users,id',
            'school_id' => 'required|string|exists:schools,id',
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'invoice_id.required' => 'The invoice is required',
            'invoice_id.exists' => 'The selected invoice does not exist',
            'student_id.required' => 'The student is required',
            'student_id.exists' => 'The selected student does not exist',
            'amount.required' => 'The payment amount is required',
            'amount.numeric' => 'The payment amount must be a number',
            'amount.min' => 'The payment amount must be at least 0.01',
            'payment_date.required' => 'The payment date is required',
            'payment_date.date' => 'The payment date must be a valid date',
            'method.required' => 'The payment method is required',
            'method.in' => 'The payment method must be one of: cash, bank_transfer, online',
            'reference.max' => 'The reference cannot exceed 100 characters',
            'notes.max' => 'The notes cannot exceed 500 characters',
            'status.in' => 'The status must be one of: pending, completed, failed',
            'processed_by.exists' => 'The selected processor does not exist',
            'school_id.required' => 'The school ID is required',
            'school_id.exists' => 'The selected school does not exist',
        ];
    }
}