<?php

namespace App\Repositories\Chat;

use App\Models\Chat;
use App\Repositories\BaseRepository;

class ChatRepository extends BaseRepository
{
    public function __construct(Chat $model)
    {
        parent::__construct($model);
    }

    /**
     * List chats with filters.
     */
    public function list(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query()->with(['sender.school', 'receiver.school']);

        if (!empty($filters['sender_id'])) {
            $query->where('sender_id', $filters['sender_id']);
        }

        if (!empty($filters['receiver_id'])) {
            $query->where('receiver_id', $filters['receiver_id']);
        }

        if (!empty($filters['partner_id']) && \Illuminate\Support\Facades\Auth::check()) {
            $userId = \Illuminate\Support\Facades\Auth::id();
            $partnerId = $filters['partner_id'];
            $query->where(function ($q) use ($userId, $partnerId) {
                $q->where(function ($q1) use ($userId, $partnerId) {
                    $q1->where('sender_id', $userId)->where('receiver_id', $partnerId);
                })->orWhere(function ($q2) use ($userId, $partnerId) {
                    $q2->where('sender_id', $partnerId)->where('receiver_id', $userId);
                });
            });
        }

        if (isset($filters['is_read'])) {
            $query->where('is_read', (bool)$filters['is_read']);
        }

        return $query->oldest()->get();
    }

    /**
     * Mark messages from partner to user as read.
     */
    public function markAsRead(string $partnerId, string $userId): bool
    {
        return $this->query()
            ->where('sender_id', $partnerId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]) > 0;
    }
}
