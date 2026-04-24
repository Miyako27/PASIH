<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('submissions', 'pemda_name')) {
                $table->string('pemda_name')->nullable()->after('perihal');
            }

            if (! Schema::hasColumn('submissions', 'pemda_title')) {
                $table->string('pemda_title')->nullable()->after('pemda_name');
            }
        });

        if (Schema::hasColumn('submissions', 'pemda_name_pemda_title')) {
            DB::table('submissions')
                ->select(['id', 'pemda_name_pemda_title'])
                ->orderBy('id')
                ->chunkById(100, function ($items): void {
                    foreach ($items as $item) {
                        $raw = trim((string) ($item->pemda_name_pemda_title ?? ''));
                        $segments = preg_split('/\|\|/', $raw, 2) ?: [];
                        $pemdaName = trim((string) ($segments[0] ?? ''));
                        $pemdaTitle = trim((string) ($segments[1] ?? ''));

                        DB::table('submissions')
                            ->where('id', $item->id)
                            ->update([
                                'pemda_name' => $pemdaName,
                                'pemda_title' => $pemdaTitle,
                            ]);
                    }
                });

            Schema::table('submissions', function (Blueprint $table) {
                $table->dropColumn('pemda_name_pemda_title');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('submissions', 'pemda_name_pemda_title')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('pemda_name_pemda_title')->nullable()->after('perihal');
            });
        }

        DB::table('submissions')
            ->select(['id', 'pemda_name', 'pemda_title'])
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    $pemdaName = trim((string) ($item->pemda_name ?? ''));
                    $pemdaTitle = trim((string) ($item->pemda_title ?? ''));

                    DB::table('submissions')
                        ->where('id', $item->id)
                        ->update([
                            'pemda_name_pemda_title' => $pemdaName.' || '.$pemdaTitle,
                        ]);
                }
            });

        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'pemda_title')) {
                $table->dropColumn('pemda_title');
            }

            if (Schema::hasColumn('submissions', 'pemda_name')) {
                $table->dropColumn('pemda_name');
            }
        });
    }
};
