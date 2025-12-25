<?php


namespace App\Services\Chat;

use App\Repositories\Chat\ChatRepository;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatService
{
    public function __construct(public ChatRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): Chat
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Chat record not found");
        }
        return $model;
    }

    public function create(array $data): Chat
    {
        // Add sender_id from authenticated user
        $data['sender_id'] = Auth::id();

        // Add school_id from authenticated user
        if (Auth::user()->school_id) {
            $data['school_id'] = Auth::user()->school_id;
        } else {
            // If sender has no school_id (Super Admin), use receiver's school_id
            $receiver = User::find($data['receiver_id']);
            if ($receiver && $receiver->school_id) {
                $data['school_id'] = $receiver->school_id;
            }
        }

        // Security: Verify relationship linkage (Sender must be authorized to message Receiver)
        $availableContacts = $this->getAvailableContacts();
        if (!$availableContacts->pluck('id')->contains($data['receiver_id'])) {
            throw new \RuntimeException("Unauthorized: You are not permitted to message this user.");
        }

        $chat = $this->repo->create($data);

        // Load relationships for broadcasting
        $chat->load(['sender', 'receiver']);

        // Broadcast the event to both sender and receiver
        try {
            \Illuminate\Support\Facades\Log::info('Broadcasting MessageSent event', [
                'sender_id' => $chat->sender_id,
                'receiver_id' => $chat->receiver_id,
                'chat_id' => $chat->id
            ]);
            broadcast(new \App\Events\MessageSent($chat))->toOthers();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Broadcast failed', ['error' => $e->getMessage()]);
        }

        return $chat;
    }

    public function update(int|string $id, array $data): Chat
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Chat record not found");
        }
        return $model;
    }

    public function delete(int|string $id): bool
    {
        return $this->repo->delete($id);
    }

    /**
     * Get available contacts based on user role and optional student scoping.
     */
    public function getAvailableContacts(array $filters = [])
    {
        /** @var User $user */
        $user = Auth::user();
        $roles = $user->roles->pluck('name');

        // 1. Super Admin: can chat with all school admins
        if ($roles->contains('super_admin')) {
            return User::whereHas('roles', fn($q) => $q->where('name', 'school_admin'))
                ->with('school:id,name')
                ->select('id', 'name', 'email', 'school_id')
                ->get()
                ->map(function ($u) {
                    if ($u->school) $u->name = "{$u->name} ({$u->school->name})";
                    return $u;
                });
        }

        // 2. School Admin: can chat with super admin + all users in THEIR school
        if ($roles->contains('school_admin')) {
            return User::where(function ($q) use ($user) {
                $q->where('school_id', $user->school_id)
                    ->orWhereHas('roles', fn($rq) => $rq->where('name', 'super_admin'));
            })
                ->where('id', '!=', $user->id)
                ->select('id', 'name', 'email')
                ->get();
        }

        // 3. Teacher: can chat with Students (in their classes), Guardians (of those students), and School Admin
        if ($roles->contains('teacher')) {
            $classIds = \App\Models\TeacherSubject::where('teacher_id', $user->teacherProfile?->id ?? 0)
                ->pluck('class_id')
                ->unique();

            return User::where('school_id', $user->school_id)
                ->where(function ($q) use ($classIds) {
                    $q->whereHas('roles', fn($rq) => $rq->where('name', 'school_admin'))
                        ->orWhereHas('student', fn($sq) => $sq->whereIn('class_id', $classIds))
                        ->orWhereHas('guardian.students', fn($gq) => $gq->whereIn('class_id', $classIds));
                })
                ->where('id', '!=', $user->id)
                ->select('id', 'name', 'email')
                ->get();
        }

        // 4. Students & Guardians: Can see Teachers (of their class) and School Admin
        if ($roles->contains('student') || $roles->contains('guardian')) {
            $studentId = $filters['student_id'] ?? null;

            // If guardian and no specific student selected, get classes for ALL their children
            if ($roles->contains('guardian') && !$studentId) {
                $classIds = $user->guardian->students->pluck('class_id')->filter();
            } else {
                // Specific student (or just the student user themselves)
                $targetStudent = $studentId ? \App\Models\Student::find($studentId) : $user->student;
                $classIds = $targetStudent && $targetStudent->class_id ? [$targetStudent->class_id] : [];
            }

            return User::where('school_id', $user->school_id)
                ->where(function ($q) use ($classIds) {
                    $q->whereHas('roles', fn($rq) => $rq->where('name', 'school_admin'))
                        ->orWhereHas('teacherProfile.subjects', fn($tpq) => $tpq->whereIn('class_id', $classIds));
                })
                ->where('id', '!=', $user->id)
                ->select('id', 'name', 'email')
                ->distinct()
                ->get();
        }

        return collect([]);
    }

    /**
     * Mark all messages from a partner as read for the current user.
     */
    public function markAsRead(string $partnerId): bool
    {
        return $this->repo->markAsRead($partnerId, Auth::id());
    }
}
