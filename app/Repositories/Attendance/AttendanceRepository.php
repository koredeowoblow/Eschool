<?php

namespace App\Repositories\Attendance;

use App\Models\Attendance;
use App\Repositories\BaseRepository;

class AttendanceRepository extends BaseRepository
{
    public function __construct(Attendance $model)
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
            // Security: Enforce class-scoping for teachers
            $query->whereIn('class_id', function ($q) use ($user) {
                $q->select('class_id')
                    ->from('teacher_subjects')
                    ->where('teacher_id', $user->teacherProfile?->id ?? 0);
            });
        }

        return $query;
    }

    /**
     * List attendance records with filters.
     */
    public function list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->query(); // Automatically scoped

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        return $query->with(['student.user', 'classRoom'])
            ->latest('date')
            ->paginate($filters['per_page'] ?? 15);
    }
}
