<?php

namespace App\Services\Assignments;

use App\Repositories\Assignments\AssignmentSubmissionRepository;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Auth;

class AssignmentSubmissionService
{
    public function __construct(public AssignmentSubmissionRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): AssignmentSubmission
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): AssignmentSubmission
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): AssignmentSubmission
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
