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
                'note_id' => 'required|integer|exists:lesson_notes,id',
                'file_path' => 'required|string',
                'file_type' => 'required|string|max:100',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'file_path' => 'sometimes|string',
                'file_type' => 'sometimes|string|max:100',
            ];
        }

        return [];
    }
}
