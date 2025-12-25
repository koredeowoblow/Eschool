<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ViewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes handle VIEW RENDERING ONLY.
| All data operations are handled by API routes in routes/api/*.php
|
*/

// Public Routes
Route::get('/', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');

// School Registration
Route::get('/register/school', function () {
    return view('auth.register-school');
})->name('school.register');
Route::post('/register/school', [AuthController::class, 'createSchool'])->name('school.register.submit');

// Authenticated Routes - VIEW RENDERING ONLY
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (accessible to all authenticated users)
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // View Routes (ViewController - returns views only, all CRUD via API + modals)
    Route::controller(ViewController::class)->group(function () {

        // Students
        Route::middleware(['role:super_admin|school_admin|teacher'])->group(function () {
            Route::get('/students', 'studentsIndex')->name('web.students.index');
        });

        // Teachers
        Route::middleware(['role:super_admin|school_admin'])->group(function () {
            Route::get('/teachers', 'teachersIndex')->name('web.teachers.index');
        });

        // Guardians
        Route::middleware(['role:super_admin|school_admin|teacher'])->group(function () {
            Route::get('/guardians', 'guardiansIndex')->name('web.guardians.index');
        });

        // Classes
        Route::middleware(['role:super_admin|school_admin|teacher'])->group(function () {
            Route::get('/classes', 'classesIndex')->name('web.classes.index');
            Route::get('/subjects', 'subjectsIndex')->name('web.subjects.index');
            Route::get('/sessions', 'sessionsIndex')->name('web.sessions.index');
            Route::get('/terms', 'termsIndex')->name('web.terms.index');
            Route::get('/sections', 'sectionsIndex')->name('web.sections.index');
            Route::get('/lesson-notes', 'lessonNotesIndex')->name('web.lesson_notes.index');
        });

        // Assignments
        Route::middleware(['role:super_admin|school_admin|teacher|student'])->group(function () {
            Route::get('/assignments', 'assignmentsIndex')->name('web.assignments.index');
        });

        // Assignment Submissions
        Route::middleware(['role:super_admin|school_admin|teacher|student'])->group(function () {
            Route::get('/assignment-submissions', 'assignmentSubmissionsIndex')->name('web.assignment_submissions.index');
        });

        // Attendance
        Route::middleware(['role:super_admin|school_admin|teacher|student'])->group(function () {
            Route::get('/attendance', 'attendanceIndex')->name('web.attendance.index');
        });

        // Enrollments
        Route::middleware(['role:super_admin|school_admin'])->group(function () {
            Route::get('/enrollments', 'enrollmentsIndex')->name('web.enrollments.index');
        });

        // Attachments
        Route::middleware(['role:super_admin|school_admin|teacher'])->group(function () {
            Route::get('/attachments', 'attachmentsIndex')->name('web.attachments.index');
        });

        // Library
        Route::get('/library', 'libraryIndex')->name('web.library.index');

        // Payments
        Route::middleware(['role:super_admin|school_admin|student'])->group(function () {
            Route::get('/payments', 'paymentsIndex')->name('web.payments.index');
            Route::get('/fee-types', 'feeTypesIndex')->name('web.fee_types.index');
            Route::get('/invoices', 'invoicesIndex')->name('web.invoices.index');
        });

        // Chats
        Route::get('/chats', 'chatsIndex')->name('web.chats.index');

        // Timetables
        Route::get('/timetables', 'timetablesIndex')->name('web.timetables.index');

        // Reports
        Route::middleware(['role:super_admin|school_admin|teacher'])->group(function () {
            Route::get('/reports', 'reportsIndex')->name('web.reports.index');
            Route::get('/reports/academic', 'academicReportsIndex')->name('web.reports.academic');
        });

        // Assessments
        Route::middleware(['role:super_admin|school_admin|teacher|student'])->group(function () {
            Route::get('/assessments', 'assessmentsIndex')->name('web.assessments.index');
        });

        // Results
        Route::middleware(['role:super_admin|school_admin|teacher|student'])->group(function () {
            Route::get('/results', 'resultsIndex')->name('web.results.index');
        });

        // Settings
        Route::get('/settings', 'settingsIndex')->name('web.settings.index');
    });

    // Super Admin View Routes
    Route::middleware(['role:super_admin'])->prefix('super-admin')->group(function () {
        Route::get('/schools', function () {
            return view('super_admin.schools.index');
        })->name('super_admin.schools.index');
        Route::get('/users', function () {
            return view('super_admin.users.index');
        })->name('super_admin.users.index');
        Route::get('/payments', function () {
            return view('super_admin.payments.index');
        })->name('super_admin.payments.index');
        Route::get('/settings', function () {
            return view('super_admin.settings.index');
        })->name('super_admin.settings.index');
    });
});
