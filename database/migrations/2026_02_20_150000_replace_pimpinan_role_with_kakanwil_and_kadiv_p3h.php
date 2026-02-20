<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'kakanwil'],
            ['updated_at' => $now, 'created_at' => $now]
        );

        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'kepala_divisi_p3h'],
            ['updated_at' => $now, 'created_at' => $now]
        );

        DB::table('users')
            ->where('role', 'pimpinan_p3h')
            ->update(['role' => 'kakanwil']);

        $kakanwilRoleId = DB::table('roles')->where('nama_role', 'kakanwil')->value('id_role');
        if ($kakanwilRoleId) {
            DB::table('users')
                ->where('role', 'kakanwil')
                ->update(['id_role' => $kakanwilRoleId]);
        }

        DB::table('roles')->where('nama_role', 'pimpinan_p3h')->delete();
    }

    public function down(): void
    {
        $now = now();

        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'pimpinan_p3h'],
            ['updated_at' => $now, 'created_at' => $now]
        );

        DB::table('users')
            ->where('role', 'kakanwil')
            ->update(['role' => 'pimpinan_p3h']);

        $pimpinanRoleId = DB::table('roles')->where('nama_role', 'pimpinan_p3h')->value('id_role');
        if ($pimpinanRoleId) {
            DB::table('users')
                ->where('role', 'pimpinan_p3h')
                ->update(['id_role' => $pimpinanRoleId]);
        }

        DB::table('roles')->whereIn('nama_role', ['kakanwil', 'kepala_divisi_p3h'])->delete();
    }
};

