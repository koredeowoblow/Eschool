<?php

namespace App\Repositories\ClassRooms;

use App\Models\LessonNote;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class LessonNoteRepository extends BaseRepository
{
    public function __construct(LessonNote $model)
    {
        parent::__construct($model);
    }

    /**
     * List lesson notes with filters.
     */
    public function list(array $filters = []): Collection
    {
        $query = $this->query();

        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        return $query->latest()->get();
    }
}
