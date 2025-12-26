<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Helpers\ResponseHelper;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index()
    {
        return view('dashboard');
    }

    public function stats()
    {
        $user = Auth::user();

        if (!$user) {
            return ResponseHelper::unauthorized('Unauthenticated.');
        }

        if ($user->hasRole('super_admin')) {
            $stats = $this->dashboardService->getPlatformStats();
        } elseif ($user->hasRole('Student')) {
            $stats = $this->dashboardService->getStudentStats($user->id);
        } elseif ($user->hasRole('Teacher')) {
            $stats = $this->dashboardService->getTeacherStats($user->id);
        } else {
            $stats = $this->dashboardService->getSchoolStats($user->school_id);
        }

        return ResponseHelper::success($stats, 'Statistics retrieved successfully.');
    }
}
