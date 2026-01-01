<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guardian\GuardianDashboardController;
use App\Http\Controllers\Roles\RoleManagementController;

// Public routes (rate-limited to reduce abuse/brute force)
Route::post('/create-school', [AuthController::class, 'createSchool'])
    ->middleware('throttle:5,1')
    ->name('CreateSchool');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('api.login');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.password.email');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.password.update');

Route::middleware(['auth:sanctum'])->group(function () {
    // User info
    // User info
    Route::get('/user', [AuthController::class, 'me'])->name('api.user');

    // Notifications API
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index'])->name('api.notifications.index');
        Route::get('/unread', [\App\Http\Controllers\Api\NotificationController::class, 'unread'])->name('api.notifications.unread');
        Route::post('/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
        Route::post('/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])->name('api.notifications.read.all');
        Route::delete('/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy'])->name('api.notifications.destroy');
    });

    // Dashboard stats
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');

    // Logout (API-specific name to avoid clashing with web logout route)
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Secure File Upload
    Route::post('/upload', [\App\Http\Controllers\UploadController::class, 'store'])->name('api.upload');

    // Noticeboard API
    Route::get('/noticeboard', [\App\Http\Controllers\Api\NoticeboardController::class, 'index'])->name('api.noticeboard.index');
    Route::post('/noticeboard', [\App\Http\Controllers\Api\NoticeboardController::class, 'store'])->name('api.noticeboard.store');

    // Finance API (Permission-protected)
    Route::prefix('finance')->middleware('permission:finance.view.reports')->group(function () {
        Route::get('/overview', [\App\Http\Controllers\Finance\FinanceController::class, 'index'])->name('api.finance.overview');
    });

    // Audit API (Controller handles authorization for super_admin)
    Route::prefix('audit')->group(function () {
        Route::get('/', [\App\Http\Controllers\Audit\AuditController::class, 'index'])->name('api.audit.index');
        Route::get('/stats', [\App\Http\Controllers\Audit\AuditController::class, 'stats'])->name('api.audit.stats');
        Route::get('/{id}', [\App\Http\Controllers\Audit\AuditController::class, 'show'])->name('api.audit.show');
    });

    // Guardian API (Permission-protected)
    Route::prefix('guardian')->middleware('role:Guardian')->group(function () {
        Route::get('/children', [GuardianDashboardController::class, 'getChildren'])->name('api.guardian.children');
        Route::get('/children/{studentId}/results', [GuardianDashboardController::class, 'getChildResults'])->name('api.guardian.results');
        Route::get('/children/{studentId}/attendance', [GuardianDashboardController::class, 'getChildAttendance'])->name('api.guardian.attendance');
        Route::get('/children/{studentId}/fees', [GuardianDashboardController::class, 'getChildFees'])->name('api.guardian.fees');
        Route::get('/receipts/{paymentId}', [GuardianDashboardController::class, 'downloadReceipt'])->name('api.guardian.receipt');
    });

    // Role Management API (Admin only)
    Route::prefix('roles')->middleware('role:School Admin|super_admin')->group(function () {
        Route::get('/', [RoleManagementController::class, 'index'])->name('api.roles.index');
        Route::get('/permissions', [RoleManagementController::class, 'getPermissions'])->name('api.roles.permissions');
        Route::post('/', [RoleManagementController::class, 'store'])->name('api.roles.store');
        Route::put('/{id}', [RoleManagementController::class, 'update'])->name('api.roles.update');
        Route::delete('/{id}', [RoleManagementController::class, 'destroy'])->name('api.roles.destroy');
        Route::post('/assign', [RoleManagementController::class, 'assignRole'])->name('api.roles.assign');
    });

    // System Jobs API (Super Admin)
    Route::prefix('jobs')->middleware('role:super_admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\JobController::class, 'index'])->name('api.jobs.index');
        Route::post('/retry/all', [\App\Http\Controllers\SuperAdmin\JobController::class, 'retryAll'])->name('api.jobs.retry.all');
        Route::post('/retry/{id}', [\App\Http\Controllers\SuperAdmin\JobController::class, 'retry'])->name('api.jobs.retry');
        Route::delete('/{id}', [\App\Http\Controllers\SuperAdmin\JobController::class, 'destroy'])->name('api.jobs.destroy');
    });
});
