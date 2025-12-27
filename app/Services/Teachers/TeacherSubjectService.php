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
        return $this->repo->findById($id);
    }

    public function update(int|string $id, array $data): TeacherSubject
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
