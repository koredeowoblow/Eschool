<?php

namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt',
                'max:10240',
                'extensions:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt',
            ],
        ];
    }
}
