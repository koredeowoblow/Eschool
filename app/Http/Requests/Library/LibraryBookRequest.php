<?php

namespace App\Http\Requests\Library;

use App\Http\Requests\BaseRequest;

class LibraryBookRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->hasRole('super_admin') ||
            \Illuminate\Support\Facades\Auth::user()->hasRole('School Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'nullable|string|max:100',
                'category' => 'nullable|string|max:100',
                'copies' => 'nullable|integer|min:0',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'title' => 'sometimes|string|max:255',
                'author' => 'sometimes|string|max:255',
                'isbn' => 'nullable|string|max:100',
                'category' => 'nullable|string|max:100',
                'copies' => 'sometimes|integer|min:0',
            ];
        }

        return [];
    }
}
