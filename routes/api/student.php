<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [UserController::class, 'store']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::put('/students/{id}', [UserController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
});
