<?php

use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Chat\ContactController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // // Chat
    Route::get('chats/available-contacts', [ChatController::class, 'availableContacts']);
    Route::get('chats', [ChatController::class, 'index']);
    Route::post('chats', [ChatController::class, 'store']);
    Route::get('chats/{id}', [ChatController::class, 'show']);
    Route::put('chats/{id}', [ChatController::class, 'update']);
    Route::delete('chats/{id}', [ChatController::class, 'destroy']);
    Route::patch('chats/mark-as-read', [ChatController::class, 'markAsRead']);

    Route::get('contact-messages', [ContactController::class, 'index']);
    Route::post('contact-messages', [ContactController::class, 'store']);
    Route::get('contact-messages/{id}', [ContactController::class, 'show']);
    Route::put('contact-messages/{id}', [ContactController::class, 'update']);
    Route::delete('contact-messages/{id}', [ContactController::class, 'destroy']);
});
