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

    public function get(int|string $id): Timetable
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Timetable
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Timetable
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
