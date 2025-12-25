<?php

namespace App\Services\ClassRooms;

use App\Repositories\ClassRooms\ClassRoomRepository;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Auth;

class ClassRoomService
{
    public function __construct(public ClassRoomRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): ClassRoom
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Class not found");
        }
        return $model;
    }


    public function create(array $data): ClassRoom
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): ClassRoom
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Class not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        // First check if it exists (scoping handled by repo)
        $this->get($id);
        return $this->repo->delete($id);
    }
}
