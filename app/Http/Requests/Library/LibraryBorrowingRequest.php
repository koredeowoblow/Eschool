<?php

namespace App\Http\Requests\Library;

use App\Http\Requests\BaseRequest;

class LibraryBorrowingRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user->hasRole('super_admin') ||
            $user->hasRole('School Admin') ||
            $user->hasRole('Teacher') ||
            $user->hasRole('Student');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isStudent = $user->hasRole('Student');

        if ($this->isMethod('POST')) {
            return [
                'book_id' => 'required|integer|exists:library_books,id',
                'student_id' => 'nullable|integer|exists:students,id',
                'user_id' => $isStudent ? 'nullable' : 'nullable|uuid|exists:users,id',
                'borrowed_at' => 'nullable|date',
                'due_date' => 'nullable|date|after_or_equal:borrowed_at',
                'status' => $isStudent ? 'nullable' : 'nullable|in:borrowed,returned',
            ];
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return [
                'borrowed_at' => 'nullable|date',
                'due_date' => 'nullable|date|after_or_equal:borrowed_at',
                'returned_at' => 'nullable|date|after_or_equal:borrowed_at',
                'status' => 'sometimes|in:borrowed,returned,pending',
            ];
        }

        return [];
    }
}
