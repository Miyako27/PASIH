<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.pasih');
        Paginator::defaultSimpleView('vendor.pagination.pasih');

        if (! Schema::hasTable('roles')) {
            return;
        }

        $now = now();

        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'admin'],
            ['created_at' => $now, 'updated_at' => $now]
        );

        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'ketua_tim_analisis'],
            ['created_at' => $now, 'updated_at' => $now]
        );

        $legacyPimpinanRoleId = DB::table('roles')->where('nama_role', 'pimpinan_p3h')->value('id_role');
        $legacyOperatorDivisiRoleId = DB::table('roles')->where('nama_role', 'operator_divisi_p3h')->value('id_role');
        $ketuaTimRoleId = DB::table('roles')->where('nama_role', 'ketua_tim_analisis')->value('id_role');
        $kakanwilRoleId = DB::table('roles')->where('nama_role', 'kakanwil')->value('id_role');

        if (Schema::hasTable('users')) {
            if ($legacyPimpinanRoleId && $kakanwilRoleId) {
                DB::table('users')
                    ->where('id_role', $legacyPimpinanRoleId)
                    ->update(['id_role' => $kakanwilRoleId]);
            }

            if ($legacyOperatorDivisiRoleId && $ketuaTimRoleId) {
                DB::table('users')
                    ->where('id_role', $legacyOperatorDivisiRoleId)
                    ->update(['id_role' => $ketuaTimRoleId]);
            }
        }

        DB::table('roles')->whereIn('nama_role', ['pimpinan_p3h', 'operator_divisi_p3h'])->delete();
    }
}
