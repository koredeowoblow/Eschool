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
});
