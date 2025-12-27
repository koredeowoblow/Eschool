<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentPromotionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [UserController::class, 'store']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::put('/students/{id}', [UserController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);

    // Promotions
    Route::get('/promotions', [StudentPromotionController::class, 'index']);
    Route::post('/promotions', [StudentPromotionController::class, 'store']);
});
