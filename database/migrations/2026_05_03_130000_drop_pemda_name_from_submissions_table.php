<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table): void {
            if (Schema::hasColumn('submissions', 'pemda_name')) {
                $table->dropColumn('pemda_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table): void {
            if (! Schema::hasColumn('submissions', 'pemda_name')) {
                $table->string('pemda_name')->nullable()->after('perihal');
            }
        });
    }
};
