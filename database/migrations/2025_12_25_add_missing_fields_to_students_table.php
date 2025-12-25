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
        Schema::table('students', function (Blueprint $table) {
            // class_id is already added by 2025_12_24_184821_restructure_classes_and_remove_grades_table
            // Only add the remaining fields
            $table->string('blood_group', 10)->nullable()->after('status');
            $table->string('emergency_contact')->nullable()->after('blood_group');
            $table->json('medical_conditions')->nullable()->after('emergency_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'blood_group',
                'emergency_contact',
                'medical_conditions'
            ]);
        });
    }
};
