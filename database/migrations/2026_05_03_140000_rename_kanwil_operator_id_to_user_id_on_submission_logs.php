<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_statuses', function (Blueprint $table): void {
            if (! Schema::hasColumn('submission_statuses', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('submission_id')->constrained('users')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('submission_statuses', 'kanwil_operator_id')) {
            DB::table('submission_statuses')->update([
                'user_id' => DB::raw('kanwil_operator_id'),
            ]);

            Schema::table('submission_statuses', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('kanwil_operator_id');
            });
        }

        Schema::table('submission_dispositions', function (Blueprint $table): void {
            if (! Schema::hasColumn('submission_dispositions', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('submission_id')->constrained('users')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('submission_dispositions', 'kanwil_operator_id')) {
            DB::table('submission_dispositions')->update([
                'user_id' => DB::raw('kanwil_operator_id'),
            ]);

            Schema::table('submission_dispositions', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('kanwil_operator_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('submission_statuses', function (Blueprint $table): void {
            if (! Schema::hasColumn('submission_statuses', 'kanwil_operator_id')) {
                $table->foreignId('kanwil_operator_id')->nullable()->after('submission_id')->constrained('users')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('submission_statuses', 'user_id')) {
            DB::table('submission_statuses')->update([
                'kanwil_operator_id' => DB::raw('user_id'),
            ]);

            Schema::table('submission_statuses', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('user_id');
            });
        }

        Schema::table('submission_dispositions', function (Blueprint $table): void {
            if (! Schema::hasColumn('submission_dispositions', 'kanwil_operator_id')) {
                $table->foreignId('kanwil_operator_id')->nullable()->after('submission_id')->constrained('users')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('submission_dispositions', 'user_id')) {
            DB::table('submission_dispositions')->update([
                'kanwil_operator_id' => DB::raw('user_id'),
            ]);

            Schema::table('submission_dispositions', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};
