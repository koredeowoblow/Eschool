<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin');
    }

    /**
     * List all failed jobs.
     */
    public function index()
    {
        $jobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->paginate(20);

        return ResponseHelper::success($jobs, 'Failed jobs fetched successfully');
    }

    /**
     * Retry a specific job.
     */
    public function retry($id)
    {
        Artisan::call('queue:retry', ['id' => $id]);
        return ResponseHelper::success(null, 'Job retry queued successfully');
    }

    /**
     * Retry all failed jobs.
     */
    public function retryAll()
    {
        Artisan::call('queue:retry', ['id' => 'all']);
        return ResponseHelper::success(null, 'All failed jobs queued for retry');
    }

    /**
     * Delete (Forget) a specific job.
     */
    public function destroy($id)
    {
        // queue:forget expects the ID
        Artisan::call('queue:forget', ['id' => $id]);
        return ResponseHelper::success(null, 'Failed job deleted successfully');
    }
}
