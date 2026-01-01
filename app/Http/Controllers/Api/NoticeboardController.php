<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Http\Requests\Api\NoticeboardRequest;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class NoticeboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $notices = Announcement::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $notices->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'content' => $n->content,
                'type' => $n->type,
                'created_at' => $n->created_at->diffForHumans(),
                'author_name' => $n->user->name ?? 'System',
                'author_initials' => ($n->user->name ?? 'S')[0],
            ];
        });

        return ResponseHelper::success($data, 'Notices retrieved successfully');
    }

    public function store(NoticeboardRequest $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('super_admin') && !$user->hasRole('School Admin')) {
            return ResponseHelper::error('Unauthorized', 403);
        }

        $notice = Announcement::create([
            'school_id' => $user->school_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
        ]);

        return ResponseHelper::success($notice, 'Notice published successfully', 201);
    }
}
