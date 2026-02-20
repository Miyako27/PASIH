<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'admin'],
            ['created_at' => $now, 'updated_at' => $now]
        );
    }

    public function down(): void
    {
        DB::table('roles')->where('nama_role', 'admin')->delete();
    }
};
