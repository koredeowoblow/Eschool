<?php

namespace App\Services\ClassRooms;

use App\Repositories\ClassRooms\LessonNoteRepository;
use App\Models\LessonNote;
use Illuminate\Support\Facades\Auth;

class LessonNoteService
{
    public function __construct(public LessonNoteRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): \App\Models\LessonNote
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Lesson note not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\LessonNote
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\LessonNote
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Lesson note not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
