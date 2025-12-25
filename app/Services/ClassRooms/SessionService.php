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

    public function get(int|string $id): \App\Models\Session
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Session not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Session
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Session
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Session not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
