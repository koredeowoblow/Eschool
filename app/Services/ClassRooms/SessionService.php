<?php

namespace App\Services\ClassRooms;

use App\Repositories\ClassRooms\SessionRepository;
use App\Models\Session as SchoolSession;
use Illuminate\Support\Facades\Auth;

class SessionService
{
    public function __construct(public SessionRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): SchoolSession
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): SchoolSession
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): SchoolSession
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
