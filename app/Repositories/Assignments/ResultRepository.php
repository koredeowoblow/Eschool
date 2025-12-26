<?php

namespace App\Repositories\Assignments;

use App\Models\Result;
use App\Repositories\BaseRepository;

class ResultRepository extends BaseRepository
{
    public function __construct(Result $model)
    {
        parent::__construct($model);
    }

    /**
     * Scoped query: Enforce student ownership for student users.
     */
    public function query()
    {
        $query = parent::query();
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->hasRole('student')) {
            $student = $user->student()->first();
            if ($student) {
                $query->where('student_id', $student->id);
            } else {
                $query->where('id', 0); // Safe failure for orphaned users
            }
        } elseif ($user && $user->hasRole('guardian')) {
            // Security: Enforce child-scoping for parents
            $studentIds = $user->guardianStudents()->pluck('id');
            if ($studentIds->isNotEmpty()) {
                $query->whereIn('student_id', $studentIds);
            } else {
                $query->where('id', 0); // Safe failure for orphaned guardians
            }
        } elseif ($user && $user->hasRole('teacher')) {
            // Security: Enforce student-scoping for teachers based on their classes
            $query->whereIn('student_id', function ($q) use ($user) {
                $q->select('id')
                    ->from('students')
                    ->whereIn('class_id', function ($sub) use ($user) {
                        $sub->select('class_id')
                            ->from('teacher_subjects')
                            ->where('teacher_id', $user->teacherProfile?->id ?? 0);
                    });
            });
        }

        return $query;
    }

    /**
     * List results with filters.
     */
    public function list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->query(); // Automatically scoped

        if (!empty($filters['search'])) {
            $query->whereHas('student.user', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['assessment_id'])) {
            $query->where('assessment_id', $filters['assessment_id']);
        }

        if (!empty($filters['term_id'])) {
            $query->whereHas('assessment', function ($q) use ($filters) {
                $q->where('term_id', $filters['term_id']);
            });
        }

        if (!empty($filters['class_id'])) {
            $query->whereHas('assessment', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (!empty($filters['session_id'])) {
            $query->whereHas('assessment.term', function ($q) use ($filters) {
                $q->where('session_id', $filters['session_id']);
            });
        }

        if (!empty($filters['grade'])) {
            $query->where('grade', $filters['grade']);
        }

        return $query
            ->with(['student.user', 'assessment.classRoom', 'assessment.term'])
            ->latest()
            ->paginate(pageCount());
    }
}
