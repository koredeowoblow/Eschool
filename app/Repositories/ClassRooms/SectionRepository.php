<?php

namespace App\Repositories\ClassRooms;

use App\Models\Section;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class SectionRepository extends BaseRepository
{
    public function __construct(Section $model)
    {
        parent::__construct($model);
    }

    /**
     * List sections with filters.
     */
    public function list(array $filters = []): Collection
    {
        $query = $this->query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        return $query->oldest('name')->get();
    }
}
