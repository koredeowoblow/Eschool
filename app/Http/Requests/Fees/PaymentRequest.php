<?php

namespace App\Http\Requests\Fees;

use App\Http\Requests\BaseRequest;

class PaymentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user->hasRole('super_admin') ||
            $user->hasRole('school_admin') ||
            ($this->isMethod('POST') && $user->hasRole('student'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isStudent = $user->hasRole('student');

        if ($this->isMethod('POST')) {
            return [
                'invoice_id' => 'required|integer|exists:invoices,id',
                'student_id' => $isStudent ? 'nullable' : 'required|uuid|exists:students,id',
                'amount' => 'required|numeric|min:0.01',
                'method' => 'required|string|in:cash,bank_transfer,online,pos',
                'transaction_ref' => 'nullable|string|max:100',
                'payment_date' => 'nullable|date',
                'notes' => 'nullable|string|max:500',
                'status' => $isStudent ? 'nullable' : 'nullable|string|in:completed,pending,failed',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'amount' => 'sometimes|numeric|min:0.01',
                'method' => 'sometimes|string|in:cash,bank_transfer,online,pos',
                'transaction_ref' => 'nullable|string|max:100',
                'payment_date' => 'nullable|date',
                'notes' => 'nullable|string|max:500',
                'status' => 'sometimes|string|in:completed,pending,failed,verified',
            ];
        }

        return [];
    }
}
