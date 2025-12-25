<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BulkGenerateInvoiceRequest extends FormRequest
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
            'session_id' => 'required|exists:school_sessions,id',
            'term_id'    => 'required|exists:terms,id',
            'due_date'   => 'required|date',
            'class_id'   => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',

            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
            'fee_type_ids' => 'required|array',
            'fee_type_ids.*' => 'exists:fee_types,id',
            'notes'      => 'nullable|string|max:500',
        ];
    }
}
