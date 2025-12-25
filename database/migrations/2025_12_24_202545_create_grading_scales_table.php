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
        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('min_score');
            $table->integer('max_score');
            $table->string('grade_label');
            $table->string('remark')->nullable();
            $table->boolean('is_pass')->default(true);
            $table->boolean('is_default')->default(false);
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['school_id', 'session_id', 'grade_label'], 'unique_grade_per_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_scales');
    }
};
