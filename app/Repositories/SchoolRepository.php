<?php

namespace App\Repositories;

use App\Models\School;

class SchoolRepository extends BaseRepository
{
    protected $isScopedBySchool = false;

    /**
     * SchoolRepository constructor.
     *
     * @param School $model
     */
    public function __construct(School $model)
    {
        parent::__construct($model);
    }

    /**
     * List schools with filters.
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

        return $query->with(['schoolPlan'])->withCount(['users', 'students'])->latest()->get();
    }
}
