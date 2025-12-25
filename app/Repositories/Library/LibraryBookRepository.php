<?php

namespace App\Repositories\Library;

use App\Models\LibraryBook;
use App\Repositories\BaseRepository;

class LibraryBookRepository extends BaseRepository
{
    public function __construct(LibraryBook $model)
    {
        parent::__construct($model);
    }

    /**
     * List books with filters.
     */
    public function list(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query();

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['author'])) {
            $query->where('author', 'like', '%' . $filters['author'] . '%');
        }

        if (!empty($filters['isbn'])) {
            $query->where('isbn', $filters['isbn']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return $query->latest('id')->get();
    }
}
