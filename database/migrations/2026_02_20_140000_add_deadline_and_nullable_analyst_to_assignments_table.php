<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('assignments', 'deadline_at')) {
                $table->date('deadline_at')->nullable()->after('instruction');
            }
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('analyst_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'deadline_at')) {
                $table->dropColumn('deadline_at');
            }
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('analyst_id')->nullable(false)->change();
        });
    }
};

