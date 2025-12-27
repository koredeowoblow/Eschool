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

    public function get(int|string $id): Assessment
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Assessment
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Assessment
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
