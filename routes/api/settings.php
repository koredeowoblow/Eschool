<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\SystemSettingsController;
use App\Http\Controllers\SettingsController;

Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('/settings', [SystemSettingsController::class, 'index']);
    Route::post('/settings', [SystemSettingsController::class, 'update']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/settings/grading-scale', [SettingsController::class, 'gradingScale']);
    Route::get('/settings/enums', [SettingsController::class, 'getEnums']);
});
