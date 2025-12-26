<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;


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

    // Dashboard stats
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');

    // Logout (API-specific name to avoid clashing with web logout route)
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Secure File Upload
    Route::post('/upload', [\App\Http\Controllers\UploadController::class, 'store'])->name('api.upload');

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
        Route::get('/children', [\App\Http\Controllers\Guardian\GuardianDashboardController::class, 'getChildren'])->name('api.guardian.children');
        Route::get('/children/{studentId}/results', [\App\Http\Controllers\Guardian\GuardianDashboardController::class, 'getChildResults'])->name('api.guardian.results');
        Route::get('/children/{studentId}/attendance', [\App\Http\Controllers\Guardian\GuardianDashboardController::class, 'getChildAttendance'])->name('api.guardian.attendance');
        Route::get('/children/{studentId}/fees', [\App\Http\Controllers\Guardian\GuardianDashboardController::class, 'getChildFees'])->name('api.guardian.fees');
        Route::get('/receipts/{paymentId}', [\App\Http\Controllers\Guardian\GuardianDashboardController::class, 'downloadReceipt'])->name('api.guardian.receipt');
    });

    // Role Management API (Admin only)
    Route::prefix('roles')->middleware('role:School Admin|super_admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Roles\RoleManagementController::class, 'index'])->name('api.roles.index');
        Route::get('/permissions', [\App\Http\Controllers\Roles\RoleManagementController::class, 'getPermissions'])->name('api.roles.permissions');
        Route::post('/', [\App\Http\Controllers\Roles\RoleManagementController::class, 'store'])->name('api.roles.store');
        Route::put('/{id}', [\App\Http\Controllers\Roles\RoleManagementController::class, 'update'])->name('api.roles.update');
        Route::delete('/{id}', [\App\Http\Controllers\Roles\RoleManagementController::class, 'destroy'])->name('api.roles.destroy');
        Route::post('/assign', [\App\Http\Controllers\Roles\RoleManagementController::class, 'assignRole'])->name('api.roles.assign');
    });
});
