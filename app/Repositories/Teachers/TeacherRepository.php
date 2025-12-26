<?php

namespace App\Repositories\Teachers;

use App\Models\TeacherProfile;
use App\Repositories\BaseRepository;

class TeacherRepository extends BaseRepository
{
    public function __construct(\App\Models\TeacherProfile $model)
    {
        parent::__construct($model);
    }

    /**
     * List teachers with filters.
     */
    public function list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->query()->with('user')->withCount('assignments');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['department'])) {
            $query->where('department', 'like', '%' . $filters['department'] . '%');
        }

        if (!empty($filters['qualification'])) {
            $query->where('qualification', 'like', '%' . $filters['qualification'] . '%');
        }

        if (!empty($filters['employee_number'])) {
            $query->where('employee_number', $filters['employee_number']);
        }

        return $query->orderByDesc('hire_date')->paginate(pageCount());
    }
}
