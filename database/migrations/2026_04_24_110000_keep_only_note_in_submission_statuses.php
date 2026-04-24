<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('submission_statuses')) {
            return;
        }

        if (Schema::hasColumn('submission_statuses', 'revision_note')) {
            DB::statement("UPDATE submission_statuses SET note = COALESCE(NULLIF(note, ''), NULLIF(revision_note, ''), NULLIF(rejection, ''))");
        }

        Schema::table('submission_statuses', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('submission_statuses', 'revision_note')) {
                $dropColumns[] = 'revision_note';
            }

            if (Schema::hasColumn('submission_statuses', 'rejection')) {
                $dropColumns[] = 'rejection';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('submission_statuses')) {
            return;
        }

        Schema::table('submission_statuses', function (Blueprint $table) {
            if (! Schema::hasColumn('submission_statuses', 'revision_note')) {
                $table->text('revision_note')->nullable()->after('status');
            }

            if (! Schema::hasColumn('submission_statuses', 'rejection')) {
                $table->text('rejection')->nullable()->after('revision_note');
            }
        });

        DB::statement("UPDATE submission_statuses SET revision_note = note WHERE status IN ('accepted', 'revised')");
        DB::statement("UPDATE submission_statuses SET rejection = note WHERE status = 'rejected'");
    }
};
