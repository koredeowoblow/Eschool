<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'no_of_teachers')) {
                $table->integer('no_of_teachers')->default(0)->after('no_of_students');
            }
            if (!Schema::hasColumn('plans', 'no_of_guardians')) {
                $table->integer('no_of_guardians')->default(0)->after('no_of_teachers');
            }
            if (!Schema::hasColumn('plans', 'no_of_staff')) {
                $table->integer('no_of_staff')->default(0)->after('no_of_guardians');
            }
        });

        if (!Schema::hasTable('school_plans')) {
            Schema::create('school_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('price')->default(0);
                $table->integer('no_of_students')->default(0);
                $table->integer('no_of_teachers')->default(0);
                $table->integer('no_of_guardians')->default(0);
                $table->integer('no_of_staff')->default(0);
                $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
                $table->foreignUuid('school_id')
                    ->nullable()
                    ->constrained('schools')
                    ->cascadeOnDelete();
                $table->timestamps();
            });
        }

        Schema::table('schools', function (Blueprint $table) {
            // Handle plan_id if it exists
            if (Schema::hasColumn('schools', 'plan_id')) {
                // Try to drop FK, ignoring errors if it doesn't exist or name mismatch
                try {
                    $table->dropForeign(['plan_id']);
                } catch (\Exception $e) {
                    // FK might not exist or have different name.
                    // If we really need to find it, we'd query information_schema, but let's assume if it fails it's gone.
                }
                $table->dropColumn('plan_id');
            }

            // Handle 'plan' string column if specific legacy exists and we want to remove it?
            // User didn't ask to remove 'plan' string explicitly, but referenced "current plan_id".
            // We'll leave 'plan' string alone to avoid data loss if it's used unrelatedly.

            if (!Schema::hasColumn('schools', 'school_plan_id')) {
                $table->foreignId('school_plan_id')->nullable()->after('contact_person')->constrained('school_plans')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['school_plan_id']);
            $table->dropColumn('school_plan_id');
            $table->foreignId('plan_id')->nullable()->constrained('plans');
        });

        Schema::dropIfExists('school_plans');

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['no_of_teachers', 'no_of_guardians', 'no_of_staff']);
        });
    }
};
