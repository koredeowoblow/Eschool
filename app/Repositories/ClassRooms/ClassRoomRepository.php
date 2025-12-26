<?php

namespace App\Repositories\ClassRooms;

use App\Models\ClassRoom;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class ClassRoomRepository extends BaseRepository
{
    public function __construct(ClassRoom $model)
    {
        parent::__construct($model);
    }
    /**
     * Get a scoped query for the model.
     */
    public function query()
    {
        $query = parent::query();
        $user = Auth::user();

        // Teachers can only see classes they are assigned to (via TeacherSubject)
        if ($user && $user->hasRole('Teacher')) {
            $query->whereIn('id', function ($q) use ($user) {
                $q->select('class_id')
                    ->from('teacher_subjects')
                    ->where('teacher_id', $user->teacherProfile?->id ?? 0);
            });
        }

        return $query;
    }

    /**
     * List classes with filters.
     */
    public function list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['session_id'])) {
            $query->where('session_id', $filters['session_id']);
        }

        if (!empty($filters['term_id'])) {
            $query->where('term_id', $filters['term_id']);
        }

        return $query
            ->with(['section', 'session', 'term', 'classTeacher.user'])
            ->withCount(['students', 'subjects', 'assignments'])
            ->latest('id')
            ->paginate(pageCount());
    }
}
