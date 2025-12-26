<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;

class FeeRequest extends FormRequest
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
        $rules = [
            'class_id' => 'nullable|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'session_id' => 'required|exists:school_sessions,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'fee_type' => 'required|string|in:tuition,exam,uniform,other',
            'due_date' => 'required|date|after_or_equal:today',
            'is_mandatory' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Make fields optional during update
            foreach ($rules as $field => $rule) {
                if (str_contains($rule, 'required')) {
                    $rules[$field] = 'sometimes|' . $rule;
                }
            }
        }

        return $rules;
    }

    /**
     * Sanitize input after validation.
     */
    protected function passedValidation(): void
    {
        if ($this->has('title')) {
            $this->merge(['title' => strip_tags($this->title)]);
        }
        if ($this->has('description')) {
            $this->merge(['description' => strip_tags($this->description)]);
        }
    }
}
