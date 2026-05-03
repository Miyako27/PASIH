<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('submission_statuses as ss')
            ->join('submissions as s', 's.id', '=', 'ss.submission_id')
            ->where('ss.status', 'submitted')
            ->whereNull('ss.user_id')
            ->update([
                'ss.user_id' => DB::raw('s.submitter_id'),
            ]);
    }

    public function down(): void
    {
        // No-op: data backfill is intentionally irreversible.
    }
};
