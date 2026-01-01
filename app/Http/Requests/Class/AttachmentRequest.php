<?php

namespace App\Http\Requests\Class;

use App\Http\Requests\BaseRequest;

class AttachmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('School Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'note_id' => 'required_without:class_id|nullable|integer|exists:lesson_notes,id',
                'class_id' => 'required_without:note_id|nullable|uuid|exists:grade_levels,id',
                'subject_id' => 'required_with:class_id|nullable|integer|exists:subjects,id',
                'title' => 'required_with:class_id|nullable|string|max:255',
                'file_path' => 'required|string',
                'file_type' => 'required|string|max:100',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'title' => 'sometimes|string|max:255',
                'file_path' => 'sometimes|string',
                'file_type' => 'sometimes|string|max:100',
            ];
        }

        return [];
    }
}
