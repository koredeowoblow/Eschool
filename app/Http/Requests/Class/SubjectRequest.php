<?php

namespace App\Http\Requests\Class;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Auth;

class SubjectRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->hasRole('super_admin') || Auth::user()?->hasRole('school_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('subject'); // used for unique validation on update

        $rules = [
            'name' => 'string|max:150',
            'code' => 'string|max:50|unique:subjects,code,' . $id,
        ];

        // Make 'name' and 'code' required only on POST requests
        if ($this->isMethod('post')) {
            $rules['name'] = 'required|string|max:150';
            $rules['code'] = 'required|string|max:50|unique:subjects,code,' . $id;
        }

        return $rules;
    }
}
