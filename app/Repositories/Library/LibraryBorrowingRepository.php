<?php

namespace App\Repositories\Library;

use App\Models\LibraryBorrowing;
use App\Repositories\BaseRepository;

class LibraryBorrowingRepository extends BaseRepository
{
    public function __construct(LibraryBorrowing $model)
    {
        parent::__construct($model);
    }

    /**
     * Scoped query: Enforce ownership for student/guardian users.
     */
    public function query()
    {
        $query = parent::query();
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->hasRole('Student')) {
            $query->where('user_id', $user->id);
        } elseif ($user && $user->hasRole('Guardian')) {
            // Security: Enforce child-scoping for parents
            $studentUserIds = $user->guardianStudents()->pluck('user_id');
            if ($studentUserIds->isNotEmpty()) {
                $query->whereIn('user_id', $studentUserIds);
            } else {
                $query->where('id', 0); // Safe failure for orphaned guardians
            }
        }

        return $query;
    }

    /**
     * List borrowings with filters.
     */
    public function list(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query(); // Automatically scoped

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['book_id'])) {
            $query->where('book_id', $filters['book_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('borrowed_at')->get();
    }
}
