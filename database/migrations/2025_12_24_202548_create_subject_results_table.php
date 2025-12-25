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
        Schema::create('subject_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained('school_sessions')->onDelete('cascade');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');

            $table->decimal('ca_score', 5, 2)->default(0);
            $table->decimal('exam_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);

            $table->string('grade', 10)->nullable();
            $table->string('remark')->nullable();
            $table->string('status', 20)->default('incomplete'); // Pass, Fail, Incomplete

            $table->boolean('is_collated')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->uuid('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'class_id', 'term_id', 'session_id'], 'unique_subject_result_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_results');
    }
};
