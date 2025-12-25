<?php

namespace App\Repositories\Users;

use App\Models\Guardian;
use App\Repositories\BaseRepository;

class GuardianRepository extends BaseRepository
{
    public function __construct(Guardian $model)
    {
        parent::__construct($model);
    }
}
