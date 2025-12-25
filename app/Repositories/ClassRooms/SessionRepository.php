<?php

namespace App\Repositories\ClassRooms;

use App\Models\Session;
use App\Repositories\BaseRepository;

class SessionRepository extends BaseRepository
{
    public function __construct(Session $model)
    {
        parent::__construct($model);
    }

    /**
     * List sessions with filters.
     */
    public function list(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('start_date')->get();
    }
}
