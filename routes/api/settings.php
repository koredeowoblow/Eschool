<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\SystemSettingsController;
use App\Http\Controllers\SettingsController;

Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('/settings', [SystemSettingsController::class, 'index']);
    Route::post('/settings', [SystemSettingsController::class, 'update']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/settings/enums', [SettingsController::class, 'getEnums']);

    // Grading Scale Management
    Route::get('/grading-scales', [\App\Http\Controllers\School\Academic\GradingScaleController::class, 'index']);
    Route::post('/grading-scales', [\App\Http\Controllers\School\Academic\GradingScaleController::class, 'store']);
    Route::put('/grading-scales/{id}', [\App\Http\Controllers\School\Academic\GradingScaleController::class, 'update']);
    Route::delete('/grading-scales/{id}', [\App\Http\Controllers\School\Academic\GradingScaleController::class, 'destroy']);

    // Legacy/Static for fallback
    Route::get('/settings/grading-scale', [SettingsController::class, 'gradingScale']);
});
