<?php

namespace App\Services\ClassRooms;

use App\Repositories\ClassRooms\TermRepository;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;

class TermService
{
    public function __construct(public TermRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): Term
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Term
    {
        $data['school_id'] = Auth::user()->school_id;
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Term
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
