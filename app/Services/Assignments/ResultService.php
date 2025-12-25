<?php

namespace App\Services\Assignments;

use App\Repositories\Assignments\ResultRepository;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class ResultService
{
    public function __construct(public ResultRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): \App\Models\Result
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Result not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Result
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Result
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Result not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
