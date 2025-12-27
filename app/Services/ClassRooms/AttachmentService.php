<?php

namespace App\Services\ClassRooms;

use App\Repositories\ClassRooms\AttachmentRepository;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;

class AttachmentService
{
    public function __construct(public AttachmentRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): Attachment
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): Attachment
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): Attachment
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
