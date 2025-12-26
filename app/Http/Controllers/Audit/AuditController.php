<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    /**
     * Display audit logs (Admin only)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Super admin can view all logs, others need permission
        if (!$user->hasRole('super_admin') && !$user->can('audit.view.logs')) {
            return $this->error('Unauthorized to view audit logs', 403);
        }

        $query = AuditLog::query()->with(['user', 'school']);

        // Super admin sees all schools, others only their school
        if (!$user->hasRole('super_admin')) {
            $query->where('school_id', $user->school_id);
        }

        $query->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('entity', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate($request->get('per_page', 50));

        return $this->success($logs);
    }

    /**
     * Show detailed audit log entry
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->hasRole('super_admin') && !$user->can('audit.view.logs')) {
            return $this->error('Unauthorized', 403);
        }

        $query = AuditLog::query()->with(['user', 'school']);

        // Super admin sees all, others only their school
        if (!$user->hasRole('super_admin')) {
            $query->where('school_id', $user->school_id);
        }

        $log = $query->findOrFail($id);

        return $this->success($log);
    }

    /**
     * Get audit statistics
     */
    public function stats(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('super_admin') && !$user->can('audit.view.logs')) {
            return $this->error('Unauthorized', 403);
        }

        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);

        $query = AuditLog::query()->where('created_at', '>=', $startDate);

        // Super admin sees all schools
        if (!$user->hasRole('super_admin')) {
            $query->where('school_id', $user->school_id);
        }

        $stats = [
            'total_actions' => (clone $query)->count(),

            'by_action' => (clone $query)
                ->selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action'),

            'by_entity' => (clone $query)
                ->selectRaw('entity, COUNT(*) as count')
                ->groupBy('entity')
                ->pluck('count', 'entity'),

            'recent_unauthorized' => (clone $query)
                ->where('action', 'unauthorized')
                ->count(),
        ];

        return $this->success($stats);
    }
}
