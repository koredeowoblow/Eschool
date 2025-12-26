<?php

namespace App\Repositories\Plans;

use App\Repositories\BaseRepository;
use App\Models\SchoolPlan;

class SchoolPlanRepository extends BaseRepository
{
    protected $isScopedBySchool = true;

    public function __construct(SchoolPlan $model)
    {
        parent::__construct($model);
    }
}
