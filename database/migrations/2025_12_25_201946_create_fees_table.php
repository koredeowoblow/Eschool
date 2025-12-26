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
        if (!Schema::hasTable('fees')) {
            Schema::create('fees', function (Blueprint $table) {
                $table->id();
                $table->uuid('school_id');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
                $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
                $table->foreignId('session_id')->constrained('school_sessions')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('amount', 12, 2);
                $table->string('fee_type'); // tuition, exam, levy, etc.
                $table->date('due_date');
                $table->boolean('is_mandatory')->default(true);
                $table->uuid('created_by');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
