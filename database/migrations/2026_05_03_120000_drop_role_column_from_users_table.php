<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('roles')) {
            return;
        }

        if (Schema::hasColumn('users', 'role')) {
            $roles = DB::table('roles')->pluck('id_role', 'nama_role');

            foreach ($roles as $roleName => $roleId) {
                DB::table('users')
                    ->where('role', $roleName)
                    ->where(function ($query) {
                        $query->whereNull('id_role');
                    })
                    ->update(['id_role' => $roleId]);
            }

            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('role');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role')->nullable()->after('password');
            });
        }

        if (Schema::hasTable('roles') && Schema::hasColumn('users', 'id_role')) {
            $users = DB::table('users')
                ->leftJoin('roles', 'users.id_role', '=', 'roles.id_role')
                ->select('users.id', 'roles.nama_role')
                ->get();

            foreach ($users as $user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role' => $user->nama_role]);
            }
        }
    }
};
