<?php

namespace App\Repositories\Plans;

use App\Repositories\BaseRepository;
use App\Models\Plan;

class PlanRepository extends BaseRepository
{
    protected $isScopedBySchool = false; // Plans are generally global or linked specifically, but not implicitly scoped to current user's school for browsing (except for custom plans maybe)

    public function __construct(Plan $model)
    {
        parent::__construct($model);
    }
}
