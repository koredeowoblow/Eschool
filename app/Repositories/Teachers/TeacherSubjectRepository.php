<?php

namespace App\Repositories\Teachers;

use App\Models\TeacherSubject;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class TeacherSubjectRepository extends BaseRepository
{
    public function __construct(TeacherSubject $model)
    {
        parent::__construct($model);
    }

    public function list(array $filters = [])
    {
        $query = $this->model->newQuery();
        $user = Auth::user();

        // Scope to teacher if role is teacher
        if ($user && $user->hasRole('teacher')) {
            $query->where('teacher_id', $user->teacherProfile?->id ?? 0);
        }

        if (isset($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (isset($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (isset($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('teacher.user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('classRoom', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subject', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->with(['teacher.user', 'classRoom.section', 'subject'])->latest()->get();
    }
}
