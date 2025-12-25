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

    public function get(int|string $id): \App\Models\Attachment
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Attachment not found");
        }
        return $model;
    }

    public function create(array $data): \App\Models\Attachment
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): \App\Models\Attachment
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Attachment not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        $this->get($id);
        return $this->repo->delete($id);
    }
}
