<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Library\LibraryController;
use App\Http\Controllers\Library\LibraryBorrowingController;



Route::middleware(['auth:sanctum'])->group(function () {
    // // Library
    Route::get('library/books', [LibraryController::class, 'index']);
    Route::post('library/books', [LibraryController::class, 'store']);
    Route::get('library/books/{id}', [LibraryController::class, 'show']);
    Route::put('library/books/{id}', [LibraryController::class, 'update']);
    Route::delete('library/books/{id}', [LibraryController::class, 'destroy']);

    Route::get('library/borrowings', [LibraryBorrowingController::class, 'index']);
    Route::post('library/borrowings', [LibraryBorrowingController::class, 'store']);
    Route::get('library/borrowings/{id}', [LibraryBorrowingController::class, 'show']);
    Route::put('library/borrowings/{id}', [LibraryBorrowingController::class, 'update']);
    Route::delete('library/borrowings/{id}', [LibraryBorrowingController::class, 'destroy']);
});
