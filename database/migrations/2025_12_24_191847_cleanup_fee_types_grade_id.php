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
            if (Schema::hasColumn('fee_types', 'grade_id')) {
                try {
                    $table->dropForeign(['grade_id']);
                } catch (\Exception $e) {
                }
                $table->dropColumn('grade_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_types', function (Blueprint $table) {
            $table->unsignedBigInteger('grade_id')->nullable();
        });
    }
};
