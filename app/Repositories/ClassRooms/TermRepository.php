<?php

namespace App\Repositories\ClassRooms;

use App\Models\Term;
use App\Repositories\BaseRepository;

class TermRepository extends BaseRepository
{
    public function __construct(Term $model)
    {
        parent::__construct($model);
    }

    /**
     * List terms with filters.
     */
    public function list(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query();

        if (!empty($filters['session_id'])) {
            $query->where('session_id', $filters['session_id']);
        }

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('start_date')->get();
    }
}
