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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Action details
            $table->string('action', 50)->index(); // create, update, delete, unauthorized
            $table->string('entity', 100)->index(); // student, teacher, class, etc.
            $table->string('entity_id')->nullable()->index(); // UUID or ID of the entity

            // User details
            $table->uuid('user_id')->nullable()->index();
            $table->string('user_email')->nullable();
            $table->string('user_role', 50)->nullable();

            // School context
            $table->uuid('school_id')->nullable()->index();

            // Request details
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6
            $table->text('user_agent')->nullable();

            // Additional context (JSON)
            $table->json('metadata')->nullable(); // For additional data like changes, admission_number, etc.

            // Timestamps
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at')->nullable();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            // Indexes for common queries
            $table->index(['school_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['entity', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
