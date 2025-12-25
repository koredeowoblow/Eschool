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
        Schema::table('fee_types', function (Blueprint $table) {
            // Removed grade_id foreign key since grades table was removed in later migration
            // Only add session_id and term_id foreign keys
            $table->foreignId('session_id')->nullable()->constrained('school_sessions')->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_types', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropForeign(['term_id']);
            $table->dropColumn(['session_id', 'term_id']);
        });
    }
};
