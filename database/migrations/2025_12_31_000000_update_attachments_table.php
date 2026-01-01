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
        Schema::table('attachments', function (Blueprint $table) {
            try {
                $table->dropForeign(['note_id']);
            } catch (\Exception $e) {
                // Ignore if foreign key doesn't exist
            }
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->bigInteger('note_id')->unsigned()->nullable()->change();

            if (!Schema::hasColumn('attachments', 'title')) {
                $table->string('title')->nullable()->after('note_id');
            }
            if (!Schema::hasColumn('attachments', 'class_id')) {
                $table->foreignId('class_id')->nullable()->after('title')->constrained('classes')->onDelete('cascade');
            }
            if (!Schema::hasColumn('attachments', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->after('class_id')->constrained('subjects')->onDelete('cascade');
            }
        });

        Schema::table('attachments', function (Blueprint $table) {
            try {
                $table->foreign('note_id')->references('id')->on('lesson_notes')->onDelete('cascade');
            } catch (\Exception $e) {
                // Ignore if it fails (maybe already exists)
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            try {
                $table->dropForeign(['class_id']);
            } catch (\Exception $e) {
            }
            try {
                $table->dropForeign(['subject_id']);
            } catch (\Exception $e) {
            }
            try {
                $table->dropForeign(['note_id']);
            } catch (\Exception $e) {
            }

            $cols = [];
            if (Schema::hasColumn('attachments', 'title')) $cols[] = 'title';
            if (Schema::hasColumn('attachments', 'class_id')) $cols[] = 'class_id';
            if (Schema::hasColumn('attachments', 'subject_id')) $cols[] = 'subject_id';

            if (!empty($cols)) $table->dropColumn($cols);
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->bigInteger('note_id')->unsigned()->nullable(false)->change();
        });

        Schema::table('attachments', function (Blueprint $table) {
            try {
                $table->foreign('note_id')->references('id')->on('lesson_notes')->onDelete('cascade');
            } catch (\Exception $e) {
            }
        });
    }
};
