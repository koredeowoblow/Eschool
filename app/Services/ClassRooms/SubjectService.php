<?php

namespace App\Services\ClassRooms;

use App\Repositories\ClassRooms\SubjectRepository;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SubjectService
{
    public function __construct(public SubjectRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): \App\Models\Subject
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Subject not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Subject
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Subject
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Subject not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
