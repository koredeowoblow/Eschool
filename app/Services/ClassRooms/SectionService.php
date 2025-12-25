<?php

namespace App\Services\ClassRooms;

use App\Repositories\ClassRooms\SectionRepository;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;

class SectionService
{
    public function __construct(public SectionRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): \App\Models\Section
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Section not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Section
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Section
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Section not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
