<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Fees\FeeController;
use App\Http\Controllers\Api\Fees\FeeAssignmentController;
use App\Http\Controllers\Api\Fees\FeePaymentController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Fees CRUD
    Route::get('fees', [FeeController::class, 'index']);
    Route::post('fees', [FeeController::class, 'store']);
    Route::get('fees/{id}', [FeeController::class, 'show']);
    Route::put('fees/{id}', [FeeController::class, 'update']);
    Route::delete('fees/{id}', [FeeController::class, 'destroy']);

    // Assignments
    Route::post('fees/assign', [FeeAssignmentController::class, 'assign']);
    Route::post('students/{id}/sync-fees', [FeeAssignmentController::class, 'sync']);

    // Payments
    Route::get('fee-payments', [FeePaymentController::class, 'index']);
    Route::post('fee-payments', [FeePaymentController::class, 'store']);
    Route::get('students/{id}/outstanding-fees', [FeePaymentController::class, 'outstandingFees']);
});
