<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_analysis_approvals', function (Blueprint $table) {
            if (! Schema::hasColumn('assignment_analysis_approvals', 'assignment_statuses_id')) {
                $table->foreignId('assignment_statuses_id')
                    ->nullable()
                    ->after('assigned_by_id')
                    ->constrained('assignment_statuses')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('assignment_analysis_approvals', 'note')) {
                $table->text('note')->nullable()->after('assignment_statuses_id');
            }
        });

        DB::table('assignment_analysis_approvals')
            ->select([
                'id',
                'assignment_id',
                'revision_note',
                'approved_by_kadiv_at',
                'approved_by_kakanwil_at',
                'created_at',
            ])
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    $targetStatus = null;
                    $eventTime = $row->created_at;
                    $note = $row->revision_note;

                    if (! is_null($row->approved_by_kakanwil_at)) {
                        $targetStatus = 'completed';
                        $eventTime = $row->approved_by_kakanwil_at;
                        $note = null;
                    } elseif (! is_null($row->approved_by_kadiv_at)) {
                        $targetStatus = 'pending_kakanwil_approval';
                        $eventTime = $row->approved_by_kadiv_at;
                        $note = null;
                    } elseif (filled($row->revision_note)) {
                        $targetStatus = 'revision_by_pic';
                    }

                    $statusLogId = null;
                    if ($targetStatus !== null) {
                        $statusLogId = DB::table('assignment_statuses')
                            ->where('assignment_id', $row->assignment_id)
                            ->where('status', $targetStatus)
                            ->when($eventTime !== null, function ($query) use ($eventTime) {
                                $query->where('created_at', '<=', $eventTime);
                            })
                            ->orderByDesc('id')
                            ->value('id');
                    }

                    DB::table('assignment_analysis_approvals')
                        ->where('id', $row->id)
                        ->update([
                            'assignment_statuses_id' => $statusLogId,
                            'note' => $note,
                            'created_at' => $eventTime ?? $row->created_at,
                            'updated_at' => $eventTime ?? $row->created_at,
                        ]);
                }
            });

        Schema::table('assignment_analysis_approvals', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['revision_note', 'approved_by_kadiv_at', 'approved_by_kakanwil_at'] as $column) {
                if (Schema::hasColumn('assignment_analysis_approvals', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }

    public function down(): void
    {
        Schema::table('assignment_analysis_approvals', function (Blueprint $table) {
            if (! Schema::hasColumn('assignment_analysis_approvals', 'revision_note')) {
                $table->text('revision_note')->nullable()->after('assigned_by_id');
            }
            if (! Schema::hasColumn('assignment_analysis_approvals', 'approved_by_kadiv_at')) {
                $table->timestamp('approved_by_kadiv_at')->nullable()->after('revision_note');
            }
            if (! Schema::hasColumn('assignment_analysis_approvals', 'approved_by_kakanwil_at')) {
                $table->timestamp('approved_by_kakanwil_at')->nullable()->after('approved_by_kadiv_at');
            }
        });

        DB::table('assignment_analysis_approvals')
            ->select(['id', 'assignment_statuses_id', 'note', 'created_at'])
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    $status = null;
                    if (! is_null($row->assignment_statuses_id)) {
                        $status = DB::table('assignment_statuses')
                            ->where('id', $row->assignment_statuses_id)
                            ->value('status');
                    }

                    $revisionNote = null;
                    $kadivAt = null;
                    $kakanwilAt = null;

                    if ($status === 'revision_by_pic') {
                        $revisionNote = $row->note;
                    } elseif ($status === 'pending_kakanwil_approval') {
                        $kadivAt = $row->created_at;
                    } elseif ($status === 'completed') {
                        $kakanwilAt = $row->created_at;
                    }

                    DB::table('assignment_analysis_approvals')
                        ->where('id', $row->id)
                        ->update([
                            'revision_note' => $revisionNote,
                            'approved_by_kadiv_at' => $kadivAt,
                            'approved_by_kakanwil_at' => $kakanwilAt,
                        ]);
                }
            });

        Schema::table('assignment_analysis_approvals', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_analysis_approvals', 'assignment_statuses_id')) {
                $table->dropConstrainedForeignId('assignment_statuses_id');
            }

            if (Schema::hasColumn('assignment_analysis_approvals', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
