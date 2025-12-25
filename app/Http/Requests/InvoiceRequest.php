<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InvoiceRequest extends FormRequest
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
            'student_id' => 'required|string|exists:students,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'nullable|string|max:500',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'school_id' => 'required|string|exists:schools,id',
            'items' => 'sometimes|array',
            'items.*.fee_type_id' => 'required_with:items|string|exists:fee_types,id',
            'items.*.amount' => 'required_with:items|numeric|min:0',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.description' => 'nullable|string|max:255',
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
            'student_id.required' => 'The student is required',
            'student_id.exists' => 'The selected student does not exist',
            'invoice_date.required' => 'The invoice date is required',
            'invoice_date.date' => 'The invoice date must be a valid date',
            'due_date.required' => 'The due date is required',
            'due_date.date' => 'The due date must be a valid date',
            'due_date.after_or_equal' => 'The due date must be on or after the invoice date',
            'discount_amount.numeric' => 'The discount amount must be a number',
            'discount_amount.min' => 'The discount amount cannot be negative',
            'discount_percentage.numeric' => 'The discount percentage must be a number',
            'discount_percentage.min' => 'The discount percentage cannot be negative',
            'discount_percentage.max' => 'The discount percentage cannot exceed 100%',
            'school_id.required' => 'The school ID is required',
            'school_id.exists' => 'The selected school does not exist',
            'items.array' => 'The items must be an array',
            'items.*.fee_type_id.required_with' => 'The fee type is required for each item',
            'items.*.fee_type_id.exists' => 'One or more selected fee types do not exist',
            'items.*.amount.required_with' => 'The amount is required for each item',
            'items.*.amount.numeric' => 'The amount must be a number',
            'items.*.amount.min' => 'The amount cannot be negative',
            'items.*.quantity.required_with' => 'The quantity is required for each item',
            'items.*.quantity.integer' => 'The quantity must be an integer',
            'items.*.quantity.min' => 'The quantity must be at least 1',
        ];
    }
}