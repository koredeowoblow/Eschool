<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;

class FeeAssignmentRequest extends FormRequest
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
            'fee_id' => 'required|exists:fees,id',
            'class_id' => 'nullable|required_without:student_id|exists:classes,id',
            'student_id' => 'nullable|required_without:class_id|exists:students,id',
        ];
    }
}
