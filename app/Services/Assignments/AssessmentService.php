<?php

namespace App\Services\Assignments;

use App\Repositories\Assignments\AssessmentRepository;
use App\Models\Assessment;
use Illuminate\Support\Facades\Auth;

class AssessmentService
{
    public function __construct(public AssessmentRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): \App\Models\Assessment
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Assessment not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Assessment
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Assessment
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Assessment not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
