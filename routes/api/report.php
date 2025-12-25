<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Academic\ReportController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['role:super_admin|school_admin|teacher'])->group(function () {
        Route::post('/reports/collate', [ReportController::class, 'collate']);
        Route::get('/reports/missing', [ReportController::class, 'missing']);
        Route::get('/reports/broadsheet', [ReportController::class, 'broadsheet']);
    });
});
