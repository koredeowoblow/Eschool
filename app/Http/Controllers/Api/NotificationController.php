<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->paginate($request->get('per_page', 20));

        return ResponseHelper::success($notifications, 'Notifications fetched successfully');
    }

    /**
     * Get unread notifications for the authenticated user.
     */
    public function unread(Request $request)
    {
        $user = $request->user();
        $notifications = $user->unreadNotifications()->get();

        return ResponseHelper::success($notifications, 'Unread notifications fetched successfully');
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return ResponseHelper::success(null, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return ResponseHelper::success(null, 'All notifications marked as read');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return ResponseHelper::success(null, 'Notification deleted successfully');
    }
}
