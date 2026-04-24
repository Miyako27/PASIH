<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_pic_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('pic_assigned_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('analyst_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('deadline_at')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_analysis_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('assigned_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('revision_note')->nullable();
            $table->timestamp('approved_by_kadiv_at')->nullable();
            $table->timestamp('approved_by_kakanwil_at')->nullable();
            $table->timestamps();
        });

        DB::table('assignments')
            ->select([
                'id',
                'assigned_by_id',
                'analyst_id',
                'pic_assigned_by_id',
                'deadline_at',
                'revision_note',
                'assigned_at',
                'pic_assigned_at',
                'started_at',
                'approved_by_kadiv_at',
                'approved_by_kakanwil_at',
                'completed_at',
                'created_at',
                'updated_at',
                'status',
            ])
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                $picRows = [];
                $approvalRows = [];
                $now = now();

                foreach ($items as $item) {
                    $picBaseAt = $item->pic_assigned_at ?? $item->started_at ?? $item->assigned_at ?? $item->created_at ?? $now;

                    if (! is_null($item->analyst_id) || ! is_null($item->pic_assigned_by_id) || ! is_null($item->deadline_at)) {
                        $picRows[] = [
                            'assignment_id' => $item->id,
                            'pic_assigned_by_id' => $item->pic_assigned_by_id,
                            'analyst_id' => $item->analyst_id,
                            'deadline_at' => $item->deadline_at,
                            'created_at' => $picBaseAt,
                            'updated_at' => $item->updated_at ?? $picBaseAt,
                        ];
                    }

                    $hasApprovalData = ! is_null($item->revision_note)
                        || ! is_null($item->approved_by_kadiv_at)
                        || ! is_null($item->approved_by_kakanwil_at)
                        || ($item->status === 'completed' && ! is_null($item->completed_at));

                    if ($hasApprovalData) {
                        $kakanwilAt = $item->approved_by_kakanwil_at;
                        if (is_null($kakanwilAt) && $item->status === 'completed') {
                            $kakanwilAt = $item->completed_at;
                        }

                        $approvalRows[] = [
                            'assignment_id' => $item->id,
                            'assigned_by_id' => $item->assigned_by_id,
                            'revision_note' => $item->revision_note,
                            'approved_by_kadiv_at' => $item->approved_by_kadiv_at,
                            'approved_by_kakanwil_at' => $kakanwilAt,
                            'created_at' => $item->updated_at ?? $item->created_at ?? $now,
                            'updated_at' => $item->updated_at ?? $item->created_at ?? $now,
                        ];
                    }
                }

                if ($picRows !== []) {
                    DB::table('assignment_pic_updates')->insert($picRows);
                }

                if ($approvalRows !== []) {
                    DB::table('assignment_analysis_approvals')->insert($approvalRows);
                }
            });

        Schema::table('assignments', function (Blueprint $table) {
            $dropForeignColumns = [];
            foreach (['analyst_id', 'pic_assigned_by_id'] as $column) {
                if (Schema::hasColumn('assignments', $column)) {
                    $dropForeignColumns[] = $column;
                }
            }

            foreach ($dropForeignColumns as $column) {
                $table->dropConstrainedForeignId($column);
            }
        });

        Schema::table('assignments', function (Blueprint $table) {
            $dropColumns = [];
            foreach ([
                'deadline_at',
                'revision_note',
                'assigned_at',
                'pic_assigned_at',
                'started_at',
                'submitted_for_review_at',
                'approved_by_kadiv_at',
                'approved_by_kakanwil_at',
                'completed_at',
            ] as $column) {
                if (Schema::hasColumn('assignments', $column)) {
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
        Schema::table('assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('assignments', 'analyst_id')) {
                $table->foreignId('analyst_id')->nullable()->constrained('users')->nullOnDelete()->after('assigned_by_id');
            }
            if (! Schema::hasColumn('assignments', 'pic_assigned_by_id')) {
                $table->foreignId('pic_assigned_by_id')->nullable()->constrained('users')->nullOnDelete()->after('analyst_id');
            }
            if (! Schema::hasColumn('assignments', 'deadline_at')) {
                $table->date('deadline_at')->nullable()->after('instruction');
            }
            if (! Schema::hasColumn('assignments', 'revision_note')) {
                $table->text('revision_note')->nullable()->after('status');
            }
            if (! Schema::hasColumn('assignments', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('revision_note');
            }
            if (! Schema::hasColumn('assignments', 'pic_assigned_at')) {
                $table->timestamp('pic_assigned_at')->nullable()->after('assigned_at');
            }
            if (! Schema::hasColumn('assignments', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('pic_assigned_at');
            }
            if (! Schema::hasColumn('assignments', 'submitted_for_review_at')) {
                $table->timestamp('submitted_for_review_at')->nullable()->after('started_at');
            }
            if (! Schema::hasColumn('assignments', 'approved_by_kadiv_at')) {
                $table->timestamp('approved_by_kadiv_at')->nullable()->after('submitted_for_review_at');
            }
            if (! Schema::hasColumn('assignments', 'approved_by_kakanwil_at')) {
                $table->timestamp('approved_by_kakanwil_at')->nullable()->after('approved_by_kadiv_at');
            }
            if (! Schema::hasColumn('assignments', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('approved_by_kakanwil_at');
            }
        });

        if (Schema::hasTable('assignment_pic_updates')) {
            $latestPic = DB::table('assignment_pic_updates as apu')
                ->join(DB::raw('(SELECT assignment_id, MAX(id) AS max_id FROM assignment_pic_updates GROUP BY assignment_id) as latest_apu'), 'latest_apu.max_id', '=', 'apu.id')
                ->select(['apu.assignment_id', 'apu.pic_assigned_by_id', 'apu.analyst_id', 'apu.deadline_at', 'apu.created_at'])
                ->get();

            foreach ($latestPic as $row) {
                DB::table('assignments')
                    ->where('id', $row->assignment_id)
                    ->update([
                        'pic_assigned_by_id' => $row->pic_assigned_by_id,
                        'analyst_id' => $row->analyst_id,
                        'deadline_at' => $row->deadline_at,
                        'pic_assigned_at' => $row->created_at,
                        'started_at' => $row->created_at,
                    ]);
            }
        }

        if (Schema::hasTable('assignment_analysis_approvals')) {
            $latestApproval = DB::table('assignment_analysis_approvals as aaa')
                ->join(DB::raw('(SELECT assignment_id, MAX(id) AS max_id FROM assignment_analysis_approvals GROUP BY assignment_id) as latest_aaa'), 'latest_aaa.max_id', '=', 'aaa.id')
                ->select(['aaa.assignment_id', 'aaa.revision_note', 'aaa.approved_by_kadiv_at', 'aaa.approved_by_kakanwil_at'])
                ->get();

            foreach ($latestApproval as $row) {
                DB::table('assignments')
                    ->where('id', $row->assignment_id)
                    ->update([
                        'revision_note' => $row->revision_note,
                        'approved_by_kadiv_at' => $row->approved_by_kadiv_at,
                        'approved_by_kakanwil_at' => $row->approved_by_kakanwil_at,
                        'completed_at' => $row->approved_by_kakanwil_at,
                    ]);
            }
        }

        DB::table('assignments')->update([
            'assigned_at' => DB::raw('COALESCE(assigned_at, created_at)'),
        ]);

        Schema::dropIfExists('assignment_analysis_approvals');
        Schema::dropIfExists('assignment_pic_updates');
    }
};
