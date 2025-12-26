<?php

namespace App\Repositories\Students;

use App\Models\StudentPromotion;
use App\Repositories\BaseRepository;

class StudentPromotionRepository extends BaseRepository
{
    public function __construct(StudentPromotion $model)
    {
        parent::__construct($model);
    }

    public function list(array $filters = [])
    {
        $query = $this->query();

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['from_class_id'])) {
            $query->where('from_class_id', $filters['from_class_id']);
        }

        if (!empty($filters['to_class_id'])) {
            $query->where('to_class_id', $filters['to_class_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->with(['student.user', 'fromClass', 'toClass', 'fromSession', 'toSession', 'promotedBy'])
            ->latest()
            ->paginate(pageCount());
    }
}
