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

    public function get(int|string $id): Section
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Section
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Section
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
