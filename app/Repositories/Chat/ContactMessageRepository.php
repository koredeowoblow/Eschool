<?php

namespace App\Repositories\Chat;

use App\Models\ContactMessage;
use App\Repositories\BaseRepository;

class ContactMessageRepository extends BaseRepository
{
    public function __construct(ContactMessage $model)
    {
        parent::__construct($model);
    }

    /**
     * List contact messages with filters.
     */
    public function list(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        return $query->latest()->get();
    }
}
