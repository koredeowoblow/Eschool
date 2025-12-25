<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Fix bad/unsafe table names and typos
        if (Schema::hasTable('school-sessions') && !Schema::hasTable('school_sessions')) {
            // Rename invalid table name containing hyphen to underscored name
            Schema::rename('school-sessions', 'school_sessions');
        }
        if (Schema::hasTable('School_sessions') && !Schema::hasTable('sections')) {
            // Sections table was mistakenly created as 'School_sessions'
            Schema::rename('School_sessions', 'sections');
        }

        // 2) Ensure sections table has required columns
        if (Schema::hasTable('sections')) {
            Schema::table('sections', function (Blueprint $table) {
                if (!Schema::hasColumn('sections', 'school_id')) {
                    $table->uuid('school_id')->nullable()->after('id');
                }
            });
            // Add FK in a separate call; wrap in try/catch to avoid driver issues
            try {
                Schema::table('sections', function (Blueprint $table) {
                    if (Schema::hasColumn('sections', 'school_id')) {
                        $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
                    }
                });
            } catch (\Throwable $e) {
                // ignore if FK already exists or driver limitations
            }
        }

        // 3) Add missing section_id to classes
        if (Schema::hasTable('classes')) {
            Schema::table('classes', function (Blueprint $table) {
                if (!Schema::hasColumn('classes', 'section_id') && Schema::hasTable('sections')) {
                    $table->foreignId('section_id')->nullable()->after('grade_id')->constrained('sections')->nullOnDelete();
                }
            });
        }

        // 4) Add/ensure school_id on domain tables
        $multiTenantTables = [
            'grades', 'classes', 'students', 'teacher_profiles', 'subjects', 'teacher_subjects', 'timetables',
            'attendance', 'assessments', 'results', 'assignments', 'assignment_submissions',
            'library_books', 'library_borrowings', 'contact_messages', 'chats', 'lesson_notes', 'attachments',
            'fee_types', 'invoices', 'invoice_items', 'payments', 'guardians', 'student_guardians',
            'enrollments', 'terms', 'school_sessions', 'sections'
        ];

        foreach ($multiTenantTables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'school_id')) {
                    $table->uuid('school_id')->nullable()->index()->after('id');
                }
            });

            try {
                Schema::table($tableName, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'school_id')) {
                        $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
                    }
                });
            } catch (\Throwable $e) {
                // ignore if FK already exists or driver limitations
            }
        }

        // 5) Fix foreign references to sessions/terms if needed - ensure referencing correct table name
        if (Schema::hasTable('terms')) {
            Schema::table('terms', function (Blueprint $table) {
                if (!Schema::hasColumn('terms', 'session_id') && Schema::hasTable('school_sessions')) {
                    $table->foreignId('session_id')->nullable()->constrained('school_sessions')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                if (!Schema::hasColumn('enrollments', 'session_id') && Schema::hasTable('school_sessions')) {
                    $table->foreignId('session_id')->nullable()->constrained('school_sessions')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'session_id') && Schema::hasTable('school_sessions')) {
                    $table->foreignId('session_id')->nullable()->constrained('school_sessions')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'section_id') && Schema::hasTable('sections')) {
                    $table->foreignId('section_id')->nullable()->after('grade_id')->constrained('sections')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        $multiTenantTables = [
            'grades', 'classes', 'students', 'teacher_profiles', 'subjects', 'teacher_subjects', 'timetables',
            'attendance', 'assessments', 'results', 'assignments', 'assignment_submissions',
            'library_books', 'library_borrowings', 'contact_messages', 'chats', 'lesson_notes', 'attachments',
            'fee_types', 'invoices', 'invoice_items', 'payments', 'guardians', 'student_guardians',
            'enrollments', 'terms', 'school_sessions', 'sections'
        ];

        foreach ($multiTenantTables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            // drop FK first if exists (best effort)
            try {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $fkName = $tableName . '_school_id_foreign';
                    if (Schema::hasColumn($tableName, 'school_id')) {
                        $table->dropForeign([$fkName]);
                    }
                });
            } catch (\Throwable $e) {
                // ignore
            }
            // then drop the column if exists
            if (Schema::hasColumn($tableName, 'school_id')) {
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->dropColumn('school_id');
                    });
                } catch (\Throwable $e) {
                    // ignore if driver cannot drop columns without doctrine/dbal
                }
            }
        }

        // Revert classes.section_id if we added it
        if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'section_id')) {
            try {
                Schema::table('classes', function (Blueprint $table) {
                    $table->dropForeign(['section_id']);
                });
            } catch (\Throwable $e) {}
            try {
                Schema::table('classes', function (Blueprint $table) {
                    $table->dropColumn('section_id');
                });
            } catch (\Throwable $e) {}
        }

        // Revert students.section_id if added
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'section_id')) {
            try {
                Schema::table('students', function (Blueprint $table) {
                    $table->dropForeign(['section_id']);
                });
            } catch (\Throwable $e) {}
            try {
                Schema::table('students', function (Blueprint $table) {
                    $table->dropColumn('section_id');
                });
            } catch (\Throwable $e) {}
        }

        // Rename tables back (not generally desirable, but we provide a reverse path)
        if (Schema::hasTable('sections') && !Schema::hasTable('School_sessions')) {
            Schema::rename('sections', 'School_sessions');
        }
        if (Schema::hasTable('school_sessions') && !Schema::hasTable('school-sessions')) {
            Schema::rename('school_sessions', 'school-sessions');
        }
    }
};
