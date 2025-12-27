<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\Chat\CreateRequest;
use App\Http\Requests\Chat\Chat\UpdateRequest;
use App\Http\Requests\Chat\SendMessageRequest;
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
        $chat = $this->service->get($id);
        return ResponseHelper::success($chat, 'Chat fetched successfully');
    }

    public function update(UpdateRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Chat updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Chat deleted successfully');
    }

    public function markAsRead(SendMessageRequest $request)
    {
        $validated = $request->validated();

        $this->service->markAsRead($validated['receiver_id'] ?? $validated['partner_id']);
        return ResponseHelper::success(null, 'Messages marked as read');
    }
}
