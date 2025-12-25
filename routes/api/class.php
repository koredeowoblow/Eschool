<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Class\ClassController;
use App\Http\Controllers\Class\TimetableController;
use App\Http\Controllers\Class\AttachmentController;
use App\Http\Controllers\Class\LessonNoteController;
use App\Http\Controllers\Class\Session\EnrollmentController;
use App\Http\Controllers\Class\Session\SessionController;
use App\Http\Controllers\Class\Session\SectionController;
use App\Http\Controllers\Class\Session\TermController;

Route::middleware(['auth:sanctum'])->group(function () {

    // Classes and related
    Route::get('classes', [ClassController::class, 'index']);
    Route::post('classes', [ClassController::class, 'store']);
    Route::get('classes/{id}', [ClassController::class, 'show']);
    Route::put('classes/{id}', [ClassController::class, 'update']);
    Route::delete('classes/{id}', [ClassController::class, 'destroy']);

    Route::get('lesson-notes', [LessonNoteController::class, 'index']);
    Route::post('lesson-notes', [LessonNoteController::class, 'store']);
    Route::get('lesson-notes/{id}', [LessonNoteController::class, 'show']);
    Route::put('lesson-notes/{id}', [LessonNoteController::class, 'update']);
    Route::delete('lesson-notes/{id}', [LessonNoteController::class, 'destroy']);

    Route::get('timetables', [TimetableController::class, 'index']);
    Route::post('timetables', [TimetableController::class, 'store']);
    Route::get('timetables/{id}', [TimetableController::class, 'show']);
    Route::put('timetables/{id}', [TimetableController::class, 'update']);
    Route::delete('timetables/{id}', [TimetableController::class, 'destroy']);

    Route::get('enrollments', [EnrollmentController::class, 'index']);
    Route::post('enrollments', [EnrollmentController::class, 'store']);
    Route::get('enrollments/{id}', [EnrollmentController::class, 'show']);
    Route::put('enrollments/{id}', [EnrollmentController::class, 'update']);
    Route::delete('enrollments/{id}', [EnrollmentController::class, 'destroy']);

    Route::get('attachments', [AttachmentController::class, 'index']);
    Route::post('attachments', [AttachmentController::class, 'store']);
    Route::get('attachments/{id}', [AttachmentController::class, 'show']);
    Route::put('attachments/{id}', [AttachmentController::class, 'update']);
    Route::delete('attachments/{id}', [AttachmentController::class, 'destroy']);

    // School Sessions
    Route::get('school-sessions', [SessionController::class, 'index']);
    Route::post('school-sessions', [SessionController::class, 'store']);
    Route::get('school-sessions/{id}', [SessionController::class, 'show']);
    Route::put('school-sessions/{id}', [SessionController::class, 'update']);
    Route::delete('school-sessions/{id}', [SessionController::class, 'destroy']);

    // Sections
    Route::get('sections', [SectionController::class, 'index']);
    Route::post('sections', [SectionController::class, 'store']);
    Route::get('sections/{id}', [SectionController::class, 'show']);
    Route::put('sections/{id}', [SectionController::class, 'update']);
    Route::delete('sections/{id}', [SectionController::class, 'destroy']);

    // Terms
    Route::get('terms', [TermController::class, 'index']);
    Route::post('terms', [TermController::class, 'store']);
    Route::get('terms/{id}', [TermController::class, 'show']);
    Route::put('terms/{id}', [TermController::class, 'update']);
    Route::delete('terms/{id}', [TermController::class, 'destroy']);
});
