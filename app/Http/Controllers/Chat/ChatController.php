<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\Chat\CreateRequest;
use App\Http\Requests\Chat\Chat\UpdateRequest;
use App\Services\Chat\ChatService;

use App\Helpers\ResponseHelper;

class ChatController extends Controller
{
    public function __construct(private ChatService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher|student');
    }

    public function index(Request $request)
    {
        $data = $this->service->list([
            'sender_id' => $request->query('sender_id'),
            'receiver_id' => $request->query('receiver_id'),
            'partner_id' => $request->query('partner_id'),
            'is_read' => $request->query('is_read'),
        ]);
        return ResponseHelper::success($data, 'Chats fetched successfully');
    }

    public function availableContacts(Request $request)
    {
        $contacts = $this->service->getAvailableContacts([
            'student_id' => $request->query('student_id')
        ]);
        return ResponseHelper::success($contacts, 'Available contacts fetched successfully');
    }

    public function store(CreateRequest $request)
    {
        $chat = $this->service->create($request->validated());
        return ResponseHelper::success($chat, 'Message sent successfully', 201);
    }

    public function show(string $id)
    {
        try {
            $chat = $this->service->get($id);
            return ResponseHelper::success($chat, 'Chat fetched successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    public function update(UpdateRequest $request, string $id)
    {
        try {
            $updated = $this->service->update($id, $request->validated());
            return ResponseHelper::success($updated, 'Chat updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return ResponseHelper::success(null, 'Chat deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'partner_id' => 'required|uuid|exists:users,id',
        ]);

        $this->service->markAsRead($request->partner_id);
        return ResponseHelper::success(null, 'Messages marked as read');
    }
}
