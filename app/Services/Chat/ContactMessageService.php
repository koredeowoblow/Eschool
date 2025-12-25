<?php

namespace App\Services\Chat;

use App\Repositories\Chat\ContactMessageRepository;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;

class ContactMessageService
{
    public function __construct(public ContactMessageRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): ContactMessage
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Contact message not found");
        }
        return $model;
    }

    public function create(array $data): ContactMessage
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): ContactMessage
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Contact message not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        return $this->repo->delete($id);
    }
}
