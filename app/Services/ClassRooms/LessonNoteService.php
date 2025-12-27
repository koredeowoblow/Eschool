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

    public function get(int|string $id): LessonNote
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): LessonNote
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): LessonNote
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
