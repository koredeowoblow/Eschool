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

    public function get(int|string $id): Subject
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Subject
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Subject
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
