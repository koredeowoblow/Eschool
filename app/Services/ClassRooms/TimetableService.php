<?php

namespace App\Services\ClassRooms;

use App\Repositories\ClassRooms\TimetableRepository;
use App\Models\Timetable;
use Illuminate\Support\Facades\Auth;

class TimetableService
{
    public function __construct(public TimetableRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): \App\Models\Timetable
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Timetable not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Timetable
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Timetable
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Timetable not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
