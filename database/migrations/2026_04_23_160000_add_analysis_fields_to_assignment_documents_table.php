<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('assignment_documents', 'ringkasan_analisis')) {
                $table->text('ringkasan_analisis')->nullable()->after('file_size');
            }
            if (! Schema::hasColumn('assignment_documents', 'hasil_evaluasi')) {
                $table->text('hasil_evaluasi')->nullable()->after('ringkasan_analisis');
            }
            if (! Schema::hasColumn('assignment_documents', 'rekomendasi')) {
                $table->text('rekomendasi')->nullable()->after('hasil_evaluasi');
            }
        });

        $hasNotesColumn = Schema::hasColumn('assignment_documents', 'notes');
        $selectColumns = ['id', 'document_type', 'ringkasan_analisis', 'hasil_evaluasi', 'rekomendasi'];
        if ($hasNotesColumn) {
            $selectColumns[] = 'notes';
        }

        DB::table('assignment_documents')
            ->select($selectColumns)
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($hasNotesColumn): void {
                foreach ($rows as $row) {
                    if ($row->document_type !== 'hasil_analisis') {
                        continue;
                    }

                    $ringkasan = trim((string) ($row->ringkasan_analisis ?? ''));
                    $hasilEvaluasi = trim((string) ($row->hasil_evaluasi ?? ''));
                    $rekomendasi = trim((string) ($row->rekomendasi ?? ''));
                    $notes = $hasNotesColumn ? (string) ($row->notes ?? '') : '';

                    if ($ringkasan !== '' || $hasilEvaluasi !== '' || $rekomendasi !== '') {
                        continue;
                    }

                    $parsedRingkasan = '';
                    $parsedHasilEvaluasi = '';
                    $parsedRekomendasi = '';

                    if ($notes !== '') {
                        if (preg_match('/Ringkasan:\s*(.*?)\n\nHasil Evaluasi:/s', $notes, $m)) {
                            $parsedRingkasan = trim((string) ($m[1] ?? ''));
                        }
                        if (preg_match('/Hasil Evaluasi:\s*(.*?)\n\nRekomendasi:/s', $notes, $m)) {
                            $parsedHasilEvaluasi = trim((string) ($m[1] ?? ''));
                        }
                        if (preg_match('/Rekomendasi:\s*(.*)$/s', $notes, $m)) {
                            $parsedRekomendasi = trim((string) ($m[1] ?? ''));
                        }
                    }

                    DB::table('assignment_documents')
                        ->where('id', $row->id)
                        ->update([
                            'ringkasan_analisis' => $parsedRingkasan !== '' ? $parsedRingkasan : null,
                            'hasil_evaluasi' => $parsedHasilEvaluasi !== '' ? $parsedHasilEvaluasi : null,
                            'rekomendasi' => $parsedRekomendasi !== '' ? $parsedRekomendasi : null,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('assignment_documents', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_documents', 'ringkasan_analisis')) {
                $table->dropColumn('ringkasan_analisis');
            }
            if (Schema::hasColumn('assignment_documents', 'hasil_evaluasi')) {
                $table->dropColumn('hasil_evaluasi');
            }
            if (Schema::hasColumn('assignment_documents', 'rekomendasi')) {
                $table->dropColumn('rekomendasi');
            }
        });
    }
};
