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

    public function get(int|string $id): Assignment
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Assignment
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Assignment
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
