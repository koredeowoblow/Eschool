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

    public function get(int|string $id): \App\Models\AssignmentSubmission
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Submission not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\AssignmentSubmission
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\AssignmentSubmission
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Submission not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
