<?php

namespace App\Services\Attendance;

use App\Repositories\Attendance\AttendanceRepository;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceService
{
    public function __construct(public AttendanceRepository $repo) {}

    public function list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): \App\Models\Attendance
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Attendance record not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Attendance
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Attendance
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Attendance record not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
