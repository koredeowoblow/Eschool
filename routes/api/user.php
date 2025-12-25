<?php

use App\Http\Controllers\SuperAdmin\GlobalUserController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\TeacherController;
use App\Http\Controllers\User\GuardianController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Users
    Route::get('teachers', [TeacherController::class, 'index']);
    Route::post('teachers', [\App\Http\Controllers\User\UserController::class, 'store']);
    Route::get('teachers/{id}', [TeacherController::class, 'show']);
    Route::put('teachers/{id}', [TeacherController::class, 'update']);
    Route::delete('teachers/{id}', [TeacherController::class, 'destroy']);

    Route::get('guardians', [GuardianController::class, 'index']);
    Route::post('guardians', [\App\Http\Controllers\User\UserController::class, 'store']);
    Route::get('guardians/{id}', [GuardianController::class, 'show']);
    Route::put('guardians/{id}', [GuardianController::class, 'update']);
    Route::delete('guardians/{id}', [GuardianController::class, 'destroy']);

    Route::get('users', [GlobalUserController::class, 'index']);
    Route::put('users/{id}', [GlobalUserController::class, 'update']);
    Route::get('schools', SchoolController::class . '@index');
    // Route::post('schools',SchoolController::class . '@store');
    Route::get('schools/{id}', SchoolController::class . '@show');
    Route::put('schools/{id}', SchoolController::class . '@update');
    Route::delete('schools/{id}', SchoolController::class . '@destroy');
});
