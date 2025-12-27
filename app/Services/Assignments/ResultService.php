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

    public function get(int|string $id): Result
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Result
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Result
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
