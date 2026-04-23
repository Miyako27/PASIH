<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assignment_documents') && Schema::hasColumn('assignment_documents', 'notes')) {
            Schema::table('assignment_documents', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('assignment_documents') && ! Schema::hasColumn('assignment_documents', 'notes')) {
            Schema::table('assignment_documents', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('rekomendasi');
            });
        }
    }
};
