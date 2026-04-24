<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('kanwil_operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->text('revision_note')->nullable();
            $table->text('rejection')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('submission_dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('kanwil_operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('disposition_note')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasColumn('submissions', 'pemda_name_pemda_title')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('pemda_name_pemda_title')->nullable()->after('perihal');
            });
        }

        DB::table('submissions')
            ->select(['id', 'pemda_name', 'perda_title'])
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    $pemda = trim((string) ($item->pemda_name ?? ''));
                    $perda = trim((string) ($item->perda_title ?? ''));
                    DB::table('submissions')
                        ->where('id', $item->id)
                        ->update(['pemda_name_pemda_title' => $pemda.' || '.$perda]);
                }
            });

        DB::table('submissions')
            ->select(['id', 'kanwil_operator_id', 'status', 'revision_note', 'rejection_note', 'reviewed_at', 'updated_at', 'created_at'])
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                $now = now();
                $rows = [];

                foreach ($items as $item) {
                    $status = trim((string) ($item->status ?? 'submitted'));
                    $note = null;
                    if (in_array($status, ['accepted', 'revised'], true)) {
                        $note = $item->revision_note;
                    }
                    if ($status === 'rejected') {
                        $note = $item->rejection_note;
                    }

                    $createdAt = $item->reviewed_at ?? $item->updated_at ?? $item->created_at ?? $now;
                    $rows[] = [
                        'submission_id' => $item->id,
                        'kanwil_operator_id' => $item->kanwil_operator_id,
                        'status' => $status,
                        'revision_note' => in_array($status, ['accepted', 'revised'], true) ? $item->revision_note : null,
                        'rejection' => $status === 'rejected' ? $item->rejection_note : null,
                        'note' => $note,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }

                if ($rows !== []) {
                    DB::table('submission_statuses')->insert($rows);
                }
            });

        if (Schema::hasTable('dispositions')) {
            DB::table('dispositions')
                ->select(['submission_id', 'from_user_id', 'to_user_id', 'note', 'disposed_at', 'created_at'])
                ->orderBy('id')
                ->chunk(100, function ($items): void {
                    $rows = [];
                    $now = now();

                    foreach ($items as $item) {
                        $createdAt = $item->disposed_at ?? $item->created_at ?? $now;
                        $rows[] = [
                            'submission_id' => $item->submission_id,
                            'kanwil_operator_id' => $item->from_user_id,
                            'to_user_id' => $item->to_user_id,
                            'disposition_note' => $item->note,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ];
                    }

                    if ($rows !== []) {
                        DB::table('submission_dispositions')->insert($rows);
                    }
                });
        }

        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'kanwil_operator_id')) {
                $table->dropConstrainedForeignId('kanwil_operator_id');
            }
            if (Schema::hasColumn('submissions', 'division_operator_id')) {
                $table->dropConstrainedForeignId('division_operator_id');
            }
            if (Schema::hasColumn('submissions', 'assigned_by_id')) {
                $table->dropConstrainedForeignId('assigned_by_id');
            }
        });

        Schema::table('submissions', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['pemda_name', 'perda_title', 'status', 'revision_note', 'rejection_note', 'submitted_at', 'reviewed_at', 'finished_at'] as $column) {
                if (Schema::hasColumn('submissions', $column)) {
                    $dropColumns[] = $column;
                }
            }

            foreach (['kanwil_operator_id', 'division_operator_id', 'assigned_by_id'] as $column) {
                if (Schema::hasColumn('submissions', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });

        DB::table('submissions')
            ->whereNull('pemda_name_pemda_title')
            ->update(['pemda_name_pemda_title' => ' || ']);
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('submissions', 'kanwil_operator_id')) {
                $table->foreignId('kanwil_operator_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('submissions', 'division_operator_id')) {
                $table->foreignId('division_operator_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('submissions', 'assigned_by_id')) {
                $table->foreignId('assigned_by_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('submissions', 'pemda_name')) {
                $table->string('pemda_name')->nullable();
            }
            if (! Schema::hasColumn('submissions', 'perda_title')) {
                $table->string('perda_title')->nullable();
            }
            if (! Schema::hasColumn('submissions', 'status')) {
                $table->string('status')->default('submitted');
            }
            if (! Schema::hasColumn('submissions', 'revision_note')) {
                $table->text('revision_note')->nullable();
            }
            if (! Schema::hasColumn('submissions', 'rejection_note')) {
                $table->text('rejection_note')->nullable();
            }
            if (! Schema::hasColumn('submissions', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable();
            }
            if (! Schema::hasColumn('submissions', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }
            if (! Schema::hasColumn('submissions', 'finished_at')) {
                $table->timestamp('finished_at')->nullable();
            }
        });

        DB::table('submissions')
            ->select(['id', 'pemda_name_pemda_title'])
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    $segments = array_map('trim', explode(' || ', (string) $item->pemda_name_pemda_title, 2));
                    DB::table('submissions')
                        ->where('id', $item->id)
                        ->update([
                            'pemda_name' => $segments[0] ?? '',
                            'perda_title' => $segments[1] ?? '',
                        ]);
                }
            });

        if (Schema::hasTable('submission_statuses')) {
            $latestStatuses = DB::table('submission_statuses as ss')
                ->join(DB::raw('(SELECT submission_id, MAX(id) AS max_id FROM submission_statuses GROUP BY submission_id) as latest_ss'), 'latest_ss.max_id', '=', 'ss.id')
                ->select(['ss.submission_id', 'ss.kanwil_operator_id', 'ss.status', 'ss.revision_note', 'ss.rejection', 'ss.created_at'])
                ->get();

            foreach ($latestStatuses as $row) {
                DB::table('submissions')
                    ->where('id', $row->submission_id)
                    ->update([
                        'kanwil_operator_id' => $row->kanwil_operator_id,
                        'status' => $row->status,
                        'revision_note' => $row->revision_note,
                        'rejection_note' => $row->rejection,
                        'reviewed_at' => $row->created_at,
                        'finished_at' => $row->status === 'completed' ? $row->created_at : null,
                    ]);
            }
        }

        if (Schema::hasColumn('submissions', 'pemda_name_pemda_title')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropColumn('pemda_name_pemda_title');
            });
        }

        Schema::dropIfExists('submission_dispositions');
        Schema::dropIfExists('submission_statuses');
    }
};
