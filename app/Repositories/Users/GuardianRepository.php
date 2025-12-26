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

    /**
     * List guardians with filters.
     */
    public function list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->query(); // Automatically scoped through TenancyTrait

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['relation'])) {
            $query->where('relation', 'like', '%' . $filters['relation'] . '%');
        }

        return $query->with(['user', 'students'])
            ->latest('id')
            ->paginate(pageCount());
    }
}
