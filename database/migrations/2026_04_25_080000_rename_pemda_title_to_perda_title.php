<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('submissions')) {
            return;
        }

        Schema::table('submissions', function (Blueprint $table): void {
            if (Schema::hasColumn('submissions', 'pemda_title') && ! Schema::hasColumn('submissions', 'perda_title')) {
                $table->string('perda_title')->nullable()->after('pemda_name');
            }
        });

        if (Schema::hasColumn('submissions', 'pemda_title') && Schema::hasColumn('submissions', 'perda_title')) {
            DB::table('submissions')
                ->where(function ($query): void {
                    $query->whereNull('perda_title')->orWhere('perda_title', '');
                })
                ->update([
                    'perda_title' => DB::raw('pemda_title'),
                ]);
        }

        Schema::table('submissions', function (Blueprint $table): void {
            if (Schema::hasColumn('submissions', 'pemda_title')) {
                $table->dropColumn('pemda_title');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('submissions')) {
            return;
        }

        Schema::table('submissions', function (Blueprint $table): void {
            if (Schema::hasColumn('submissions', 'perda_title') && ! Schema::hasColumn('submissions', 'pemda_title')) {
                $table->string('pemda_title')->nullable()->after('pemda_name');
            }
        });

        if (Schema::hasColumn('submissions', 'perda_title') && Schema::hasColumn('submissions', 'pemda_title')) {
            DB::table('submissions')
                ->where(function ($query): void {
                    $query->whereNull('pemda_title')->orWhere('pemda_title', '');
                })
                ->update([
                    'pemda_title' => DB::raw('perda_title'),
                ]);
        }

        Schema::table('submissions', function (Blueprint $table): void {
            if (Schema::hasColumn('submissions', 'perda_title')) {
                $table->dropColumn('perda_title');
            }
        });
    }
};