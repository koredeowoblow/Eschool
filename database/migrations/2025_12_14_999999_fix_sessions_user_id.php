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
        $connection = Schema::getConnection()->getDriverName();

        // Only drop column on non-SQLite
        if ($connection !== 'sqlite') {
            Schema::table('sessions', function (Blueprint $table) {
                if (Schema::hasColumn('sessions', 'user_id')) {
                    $table->dropColumn('user_id');
                }
            });
        }

        // Add user_id only if it doesn't exist
        $columns = Schema::getColumnListing('sessions');
        if (!in_array('user_id', $columns)) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->uuid('user_id')->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = Schema::getColumnListing('sessions');
        if (in_array('user_id', $columns)) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};
