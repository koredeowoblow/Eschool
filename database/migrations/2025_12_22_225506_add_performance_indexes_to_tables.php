<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add performance indexes to improve query speed
     */
    public function up(): void
    {
        // Helper to safely add index
        $addIndex = function ($table, $columns, $name = null) {
            try {
                Schema::table($table, function (Blueprint $table) use ($columns, $name) {
                    $table->index($columns, $name);
                });
            } catch (\Exception $e) {
                // Ignore duplicate index errors
            }
        };

        // Users table indexes
        $addIndex('users', ['school_id', 'status'], 'users_school_id_status_index');

        // Students table indexes
        $addIndex('students', ['school_id', 'status'], 'students_school_id_status_index');

        // Teacher profiles table indexes
        $addIndex('teacher_profiles', 'employee_number', 'teacher_profiles_employee_number_index');

        // Invoices table indexes
        $addIndex('invoices', ['school_id', 'status'], 'invoices_school_id_status_index');
        $addIndex('invoices', 'due_date', 'invoices_due_date_index');

        // Payments table indexes
        $addIndex('payments', 'payment_date', 'payments_payment_date_index');
        $addIndex('payments', ['school_id', 'status'], 'payments_school_id_status_index');

        // Library books table indexes
        $addIndex('library_books', 'isbn', 'library_books_isbn_index');

        // Attendance table indexes
        $addIndex('attendance', 'date', 'attendance_date_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['email']);
            $table->dropIndex(['school_id', 'status']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['admission_number']);
            $table->dropIndex(['grade_id']);
            $table->dropIndex(['school_session_id']);
            $table->dropIndex(['school_id', 'status']);
        });

        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['employee_number']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['grade_id']);
            $table->dropIndex(['session_id']);
            $table->dropIndex(['term_id']);
            $table->dropIndex(['class_teacher_id']);
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['class_id']);
            $table->dropIndex(['subject_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['student_id']);
            $table->dropIndex(['session_id']);
            $table->dropIndex(['school_id', 'status']);
            $table->dropIndex(['due_date']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['invoice_id']);
            $table->dropIndex(['payment_date']);
            $table->dropIndex(['school_id', 'status']);
        });

        Schema::table('library_books', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['isbn']);
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['student_id']);
            $table->dropIndex(['class_id']);
            $table->dropIndex(['date']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['student_id']);
            $table->dropIndex(['class_id']);
            $table->dropIndex(['session_id']);
        });
    }
};
