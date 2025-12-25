<?php

namespace App\Services\ClassRooms;

use App\Repositories\ClassRooms\TermRepository;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;

class TermService
{
    public function __construct(public TermRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): Term
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Term not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Term
    {
        $data['school_id'] = Auth::user()->school_id;
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Term
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Term not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
