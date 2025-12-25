<?php

namespace App\Services\Assignments;

use App\Repositories\Assignments\AssignmentRepository;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;

class AssignmentService
{
    public function __construct(public AssignmentRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): \App\Models\Assignment
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Assignment not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Assignment
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Assignment
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
