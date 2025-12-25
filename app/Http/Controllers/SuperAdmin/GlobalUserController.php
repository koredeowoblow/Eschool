<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\GlobalUserService;
use Illuminate\Http\Request;

use App\Helpers\ResponseHelper;

class GlobalUserController extends Controller
{
    public function __construct(private GlobalUserService $userService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin');
    }

    public function index()
    {
        return ResponseHelper::success($this->userService->getAllUsers(), 'Users fetched successfully');
    }

    public function update(Request $request, $id)
    {
        try {
            $user = $this->userService->updateUser($id, $request->all());
            return ResponseHelper::success($user, 'User updated successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update user: ' . $e->getMessage());
        }
    }
}
