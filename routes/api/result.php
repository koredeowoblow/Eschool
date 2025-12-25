<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Assignment\ResultController;
use App\Http\Controllers\Assignment\AssessmentController;

// Assessments

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['role:super_admin|school_admin|teacher|student'])->group(function () {
        Route::get('/assessments', [AssessmentController::class, 'index']);
        Route::get('/assessments/{id}', [AssessmentController::class, 'show']);

        // Mutating Assessment Actions
        Route::middleware(['check.session'])->group(function () {
            Route::post('/assessments', [AssessmentController::class, 'store']);
            Route::put('/assessments/{id}', [AssessmentController::class, 'update']);
            Route::delete('/assessments/{id}', [AssessmentController::class, 'destroy']);
        });
    });

    // Results
    Route::middleware(['role:super_admin|school_admin|teacher|student'])->group(function () {
        Route::get('/results', [ResultController::class, 'index']);
        Route::get('/results/{id}', [ResultController::class, 'show']);

        // Mutating Result Actions
        Route::middleware(['check.session'])->group(function () {
            Route::post('/results', [ResultController::class, 'store']);
            Route::put('/results/{id}', [ResultController::class, 'update']);
            Route::delete('/results/{id}', [ResultController::class, 'destroy']);
        });
    });
});
