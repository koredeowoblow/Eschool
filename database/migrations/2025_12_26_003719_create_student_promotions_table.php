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
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();
            $table->uuid('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->unsignedBigInteger('from_class_id');
            $table->foreign('from_class_id')->references('id')->on('classes')->onDelete('cascade');

            $table->unsignedBigInteger('to_class_id');
            $table->foreign('to_class_id')->references('id')->on('classes')->onDelete('cascade');

            $table->unsignedBigInteger('from_session_id');
            $table->foreign('from_session_id')->references('id')->on('school_sessions')->onDelete('cascade');

            $table->unsignedBigInteger('to_session_id');
            $table->foreign('to_session_id')->references('id')->on('school_sessions')->onDelete('cascade');

            $table->enum('type', ['promote', 'repeat'])->default('promote');
            $table->uuid('promoted_by');
            $table->foreign('promoted_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
    }
};
