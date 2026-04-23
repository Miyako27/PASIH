<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('suratbalasan_documents') && Schema::hasColumn('suratbalasan_documents', 'notes')) {
            Schema::table('suratbalasan_documents', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }

        if (Schema::hasTable('submission_documents') && Schema::hasColumn('submission_documents', 'notes')) {
            Schema::table('submission_documents', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('suratbalasan_documents') && ! Schema::hasColumn('suratbalasan_documents', 'notes')) {
            Schema::table('suratbalasan_documents', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('file_size');
            });
        }

        if (Schema::hasTable('submission_documents') && ! Schema::hasColumn('submission_documents', 'notes')) {
            Schema::table('submission_documents', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('file_size');
            });
        }
    }
};
