<?php

namespace App\Services\Attendance;

use App\Repositories\Attendance\AttendanceRepository;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceService
{
    public function __construct(public AttendanceRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): Attendance
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Attendance
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Attendance
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
