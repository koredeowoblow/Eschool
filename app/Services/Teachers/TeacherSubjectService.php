<?php

namespace App\Services\Teachers;

use App\Repositories\Teachers\TeacherSubjectRepository;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;

class TeacherSubjectService
{
    public function __construct(public TeacherSubjectRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function create(array $data): TeacherSubject
    {
        // Force school_id from authenticated user
        $data['school_id'] = Auth::user()->school_id;

        return $this->repo->create($data);
    }

    public function get(int|string $id): TeacherSubject
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Assignment not found");
        }
        return $model;
    }

    public function update(int|string $id, array $data): TeacherSubject
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Assignment not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
