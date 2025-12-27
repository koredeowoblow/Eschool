<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
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
// Route::get('/logins', [AuthController::class, 'loginForm'])->name('login');
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
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // View Routes (ViewController - returns views only, all CRUD via API + modals)
    Route::controller(ViewController::class)->group(function () {

        // Students
        Route::middleware(['role:super_admin|School Admin|Teacher'])->group(function () {
            Route::get('/students', 'studentsIndex')->name('web.students.index');
            Route::get('/promotions', 'promotionsIndex')->name('web.promotions.index');
        });

        // Teachers
        Route::middleware(['role:super_admin|School Admin'])->group(function () {
            Route::get('/teachers', 'teachersIndex')->name('web.teachers.index');
        });

        // Guardians
        Route::middleware(['role:super_admin|School Admin|Teacher'])->group(function () {
            Route::get('/guardians', 'guardiansIndex')->name('web.guardians.index');
        });

        // Classes
        Route::middleware(['role:super_admin|School Admin|Teacher'])->group(function () {
            Route::get('/classes', 'classesIndex')->name('web.classes.index');
            Route::get('/subjects', 'subjectsIndex')->name('web.subjects.index');
            Route::get('/subject-assignments', 'subjectAssignmentsIndex')->name('web.subject_assignments.index');
            Route::get('/sessions', 'sessionsIndex')->name('web.sessions.index');
            Route::get('/terms', 'termsIndex')->name('web.terms.index');
            Route::get('/sections', 'sectionsIndex')->name('web.sections.index');
            Route::get('/lesson-notes', 'lessonNotesIndex')->name('web.lesson_notes.index');
        });

        // Assignments
        Route::middleware(['role:super_admin|School Admin|Teacher|Student'])->group(function () {
            Route::get('/assignments', 'assignmentsIndex')->name('web.assignments.index');
        });

        // Assignment Submissions
        Route::middleware(['role:super_admin|School Admin|Teacher|Student'])->group(function () {
            Route::get('/assignment-submissions', 'assignmentSubmissionsIndex')->name('web.assignment_submissions.index');
        });

        // Attendance
        Route::middleware(['role:super_admin|School Admin|Teacher|Student'])->group(function () {
            Route::get('/attendance', 'attendanceIndex')->name('web.attendance.index');
        });

        // Enrollments
        Route::middleware(['role:super_admin|School Admin'])->group(function () {
            Route::get('/enrollments', 'enrollmentsIndex')->name('web.enrollments.index');
        });

        // Attachments
        Route::middleware(['role:super_admin|School Admin|Teacher'])->group(function () {
            Route::get('/attachments', 'attachmentsIndex')->name('web.attachments.index');
        });

        // Library
        Route::get('/library', 'libraryIndex')->name('web.library.index');

        // Payments & Fees
        Route::middleware(['role:super_admin|School Admin|Student'])->group(function () {
            Route::get('/payments', 'paymentsIndex')->name('web.payments.index');
            Route::get('/fee-types', 'feeTypesIndex')->name('web.fee_types.index');
            Route::get('/invoices', 'invoicesIndex')->name('web.invoices.index');
        });

        // New Finance Module (Bursar / Admin)
        Route::middleware(['permission:finance.view.reports'])->group(function () {
            Route::get('/finance', [App\Http\Controllers\Finance\FinanceController::class, 'index'])->name('web.finance.index');
            // Add view routes if creating separate pages, e.g.
            // Route::get('/finance/invoices/create', ...)->name('web.finance.invoices.create');
        });

        Route::middleware(['role:super_admin|School Admin|Student'])->group(function () {
            // New Fee Module Routes (Legacy/Existing?)
            Route::get('/fees', 'feesIndex')->name('web.fees.index');
            Route::get('/fees/assign', 'feesAssignIndex')->name('web.fees.assign');
            Route::get('/fees/students/{id}', 'studentFeesOverview')->name('web.fees.student-overview');
            Route::get('/fees/payments', 'feePaymentsHistory')->name('web.fee_payments.history');
        });

        // Student Specific Fees
        Route::middleware(['role:Student'])->group(function () {
            Route::get('/my-fees', 'myFeesIndex')->name('web.fees.my-fees');
        });

        // Chats
        Route::get('/chats', 'chatsIndex')->name('web.chats.index');

        // Timetables
        Route::get('/timetables', 'timetablesIndex')->name('web.timetables.index');

        // Reports
        Route::middleware(['role:super_admin|School Admin|Teacher'])->group(function () {
            Route::get('/reports', 'reportsIndex')->name('web.reports.index');
            Route::get('/reports/academic', 'academicReportsIndex')->name('web.reports.academic');
        });

        // Staff Management
        Route::middleware(['role:super_admin|School Admin'])->group(function () {
            Route::get('/staff', 'staffIndex')->name('web.staff.index');
        });

        // Assessments
        Route::middleware(['role:super_admin|School Admin|Teacher|Student'])->group(function () {
            Route::get('/assessments', 'assessmentsIndex')->name('web.assessments.index');
        });

        // Results
        Route::middleware(['role:super_admin|School Admin|Teacher|Student'])->group(function () {
            Route::get('/results', 'resultsIndex')->name('web.results.index');
        });

        // Settings
        Route::get('/settings', 'settingsIndex')->name('web.settings.index');

        // Audit Logs (Admin only or super_admin)
        Route::get('/audit', function () {
            return view('audit.index');
        })->middleware('role:super_admin|School Admin')->name('web.audit.index');

        // Guardian Dashboard
        Route::middleware(['role:Guardian'])->group(function () {
            Route::get('/my-children', function () {
                return view('guardian.dashboard');
            })->name('web.guardian.dashboard');
        });

        // Role Management (Admin only)
        Route::middleware(['role:School Admin|super_admin'])->group(function () {
            Route::get('/roles', function () {
                return view('roles.index');
            })->name('web.roles.index');
        });
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
        Route::get('/plans', function () {
            return view('super_admin.plans.index');
        })->name('super_admin.plans.index');
        Route::get('/settings', function () {
            return view('super_admin.settings.index');
        })->name('super_admin.settings.index');
    });
});
