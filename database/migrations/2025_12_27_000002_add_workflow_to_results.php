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
        // 1. Add workflow columns to results table
        Schema::table('results', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'published', 'locked'])
                ->default('draft')
                ->after('remark');

            $table->uuid('reviewer_id')->nullable()->after('status');
            $table->foreign('reviewer_id')->references('id')->on('users')->nullOnDelete();

            $table->timestamp('submitted_at')->nullable()->after('reviewer_id');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->timestamp('published_at')->nullable()->after('reviewed_at');
        });

        // 2. Create result_versions table for audit trail
        Schema::create('result_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_id')->constrained('results')->cascadeOnDelete();
            $table->uuid('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();

            // Snapshot of the result data
            $table->json('data');

            // Who made the change
            $table->uuid('changed_by')->nullable();
            $table->foreign('changed_by')->references('id')->on('users')->nullOnDelete();

            // Why the change was made
            $table->string('action', 50); // 'created', 'updated', 'submitted', 'reviewed', 'published'
            $table->text('reason')->nullable();

            $table->timestamp('created_at');

            // Indexes
            $table->index(['result_id', 'created_at']);
            $table->index('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_versions');

        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn(['status', 'reviewer_id', 'submitted_at', 'reviewed_at', 'published_at']);
        });
    }
};
