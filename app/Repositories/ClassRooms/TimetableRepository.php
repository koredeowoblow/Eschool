<?php

namespace App\Repositories\ClassRooms;

use App\Models\Timetable;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class TimetableRepository extends BaseRepository
{
    public function __construct(Timetable $model)
    {
        parent::__construct($model);
    }

    /**
     * List timetables with filters.
     */
    public function list(array $filters = []): Collection
    {
        $query = $this->query();
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->hasRole('Student')) {
            $student = $user->student()->first();
            if ($student && $student->class_id) {
                $query->where('class_id', $student->class_id);
            } else {
                $query->where('id', 0); // No student profile or class found
            }
        } elseif (!empty($filters['class_id'])) {

            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['day_of_week'])) {
            $query->where('day_of_week', $filters['day_of_week']);
        }

        return $query->oldest('start_time')->get();
    }
}
