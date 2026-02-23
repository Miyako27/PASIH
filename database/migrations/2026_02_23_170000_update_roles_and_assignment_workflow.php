<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'ketua_tim_analisis'],
            ['created_at' => $now, 'updated_at' => $now]
        );

        $ketuaRoleId = DB::table('roles')->where('nama_role', 'ketua_tim_analisis')->value('id_role');

        DB::table('users')
            ->where('role', 'operator_divisi_p3h')
            ->update([
                'role' => 'ketua_tim_analisis',
                'id_role' => $ketuaRoleId,
            ]);

        DB::table('roles')->where('nama_role', 'operator_divisi_p3h')->delete();

        Schema::table('assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('assignments', 'pic_assigned_by_id')) {
                $table->foreignId('pic_assigned_by_id')->nullable()->after('analyst_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('assignments', 'revision_note')) {
                $table->text('revision_note')->nullable()->after('status');
            }

            if (! Schema::hasColumn('assignments', 'pic_assigned_at')) {
                $table->timestamp('pic_assigned_at')->nullable()->after('assigned_at');
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
        });
    }

    public function down(): void
    {
        $now = now();

        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'operator_divisi_p3h'],
            ['created_at' => $now, 'updated_at' => $now]
        );

        $operatorDivisiRoleId = DB::table('roles')->where('nama_role', 'operator_divisi_p3h')->value('id_role');

        DB::table('users')
            ->where('role', 'ketua_tim_analisis')
            ->update([
                'role' => 'operator_divisi_p3h',
                'id_role' => $operatorDivisiRoleId,
            ]);

        DB::table('roles')->where('nama_role', 'ketua_tim_analisis')->delete();

        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'pic_assigned_by_id')) {
                $table->dropConstrainedForeignId('pic_assigned_by_id');
            }

            if (Schema::hasColumn('assignments', 'revision_note')) {
                $table->dropColumn('revision_note');
            }

            if (Schema::hasColumn('assignments', 'pic_assigned_at')) {
                $table->dropColumn('pic_assigned_at');
            }

            if (Schema::hasColumn('assignments', 'submitted_for_review_at')) {
                $table->dropColumn('submitted_for_review_at');
            }

            if (Schema::hasColumn('assignments', 'approved_by_kadiv_at')) {
                $table->dropColumn('approved_by_kadiv_at');
            }

            if (Schema::hasColumn('assignments', 'approved_by_kakanwil_at')) {
                $table->dropColumn('approved_by_kakanwil_at');
            }
        });
    }
};
