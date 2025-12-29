<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\SchoolController;

Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::get('/schools', [SchoolController::class, 'index'])->name('api.schools.index');
    Route::get('/schools/{id}', [SchoolController::class, 'show'])->name('api.schools.show');
    Route::post('/schools', [SchoolController::class, 'store'])->name('api.schools.store');
    Route::put('/schools/{id}', [SchoolController::class, 'update'])->name('api.schools.update');
    Route::delete('/schools/{id}', [SchoolController::class, 'destroy'])->name('api.schools.destroy');
});
