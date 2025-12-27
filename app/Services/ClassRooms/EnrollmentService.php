<?php

namespace App\Services\ClassRooms;

use App\Repositories\Students\EnrollmentRepository;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EnrollmentService
{
    public function __construct(public EnrollmentRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): Enrollment
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Enrollment
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Enrollment
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
