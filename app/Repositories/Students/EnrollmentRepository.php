<?php

namespace App\Repositories\Students;

use App\Models\Enrollment;
use App\Repositories\BaseRepository;

class EnrollmentRepository extends BaseRepository
{
    /**
     * EnrollmentRepository constructor.
     *
     * @param Enrollment $model
     */
    public function __construct(Enrollment $model)
    {
        parent::__construct($model);
    }

    /**
     * List enrollments with filters.
     */
    public function list(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query();

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['session_id'])) {

            $query->where('session_id', $filters['session_id']);
        }

        if (!empty($filters['term_id'])) {
            $query->where('term_id', $filters['term_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('enrollment_date')->get();
    }
}
