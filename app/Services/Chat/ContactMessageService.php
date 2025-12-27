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
        return $this->repo->findById($id);
    }

    public function create(array $data): ContactMessage
    {
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): ContactMessage
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->repo->delete($id);
    }
}
