<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\PlanController;

Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{id}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{id}', [PlanController::class, 'destroy'])->name('plans.destroy');

    // Assignment and Custom Plans
    Route::post('/schools/{schoolId}/assign-plan', [PlanController::class, 'assignToSchool'])->name('plans.assign');
    Route::post('/schools/{schoolId}/custom-plan', [PlanController::class, 'createCustom'])->name('plans.custom');
    Route::put('/schools/{schoolId}/update-limits', [PlanController::class, 'updateSchoolLimits'])->name('plans.update-limits');
});
