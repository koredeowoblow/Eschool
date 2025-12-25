<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Assignment\AssignmentController;
use App\Http\Controllers\Assignment\AssignmentSubmissionController;


Route::middleware(['auth:sanctum'])->group(function () {
    // Assignments
    Route::middleware(['role:super_admin|school_admin|teacher|student'])->group(function () {
        Route::get('/assignments', [AssignmentController::class, 'index']);
        Route::get('/assignments/{id}', [AssignmentController::class, 'show']);

        // Mutating Assignment Actions
        Route::middleware(['check.session'])->group(function () {
            Route::post('/assignments', [AssignmentController::class, 'store']);
            Route::put('/assignments/{id}', [AssignmentController::class, 'update']);
            Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy']);
        });
    });
    // Assignment submissions
    Route::middleware(['role:super_admin|school_admin|teacher|student'])->group(function () {
        Route::get('/assignment-submissions', [AssignmentSubmissionController::class, 'index']);
        Route::get('/assignment-submissions/{id}', [AssignmentSubmissionController::class, 'show']);

        // Mutating Submission Actions
        Route::middleware(['check.session'])->group(function () {
            Route::post('/assignment-submissions', [AssignmentSubmissionController::class, 'store']);
            Route::put('/assignment-submissions/{id}', [AssignmentSubmissionController::class, 'update']);
            Route::delete('/assignment-submissions/{id}', [AssignmentSubmissionController::class, 'destroy']);
        });
    });
});
