<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Prepare classes table: Add name column
        if (!Schema::hasColumn('classes', 'name')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->string('name')->after('school_id')->nullable();
            });
        }

        // 2. Migrate existing grade names to classes name
        if (Schema::hasTable('grades') && Schema::hasColumn('classes', 'grade_id')) {
            DB::statement("UPDATE classes c JOIN grades g ON c.grade_id = g.id SET c.name = g.name");
        }

        // 3. DROP COLUMNS AND TABLE
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'grade_id')) {
                try {
                    $table->dropForeign(['grade_id']);
                } catch (\Exception $e) {
                }
                $table->dropColumn('grade_id');
            }
            // Ensure name is not null now
            $table->string('name')->nullable(false)->change();
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'grade_id')) {
                try {
                    $table->dropForeign(['grade_id']);
                } catch (\Exception $e) {
                }
                $table->dropColumn('grade_id');
            }
            if (!Schema::hasColumn('students', 'class_id')) {
                $table->foreignId('class_id')->nullable()->after('status')->constrained('classes')->onDelete('set null');
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'grade_id')) {
                try {
                    $table->dropForeign(['grade_id']);
                } catch (\Exception $e) {
                }
                $table->dropColumn('grade_id');
            }
            if (!Schema::hasColumn('enrollments', 'class_id')) {
                $table->foreignId('class_id')->nullable()->after('student_id')->constrained('classes')->onDelete('cascade');
            }
        });

        Schema::dropIfExists('grades');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->id();
                $table->uuid('school_id');
                $table->string('name');
                $table->string('description')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'grade_id')) {
                $table->unsignedBigInteger('grade_id')->nullable();
                $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }
            if (!Schema::hasColumn('students', 'grade_id')) {
                $table->unsignedBigInteger('grade_id')->nullable();
                $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade');
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }
            if (!Schema::hasColumn('enrollments', 'grade_id')) {
                $table->unsignedBigInteger('grade_id')->nullable();
                $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade');
            }
        });

        Schema::enableForeignKeyConstraints();
    }
};
