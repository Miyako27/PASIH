<?php

namespace App\Providers;

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
        if (! Schema::hasTable('roles')) {
            return;
        }

        $now = now();

        DB::table('roles')->updateOrInsert(
            ['nama_role' => 'admin'],
            ['created_at' => $now, 'updated_at' => $now]
        );

        DB::table('users')
            ->where('role', 'pimpinan_p3h')
            ->update(['role' => 'kakanwil']);

        $kakanwilRoleId = DB::table('roles')->where('nama_role', 'kakanwil')->value('id_role');
        if ($kakanwilRoleId && Schema::hasTable('users')) {
            DB::table('users')
                ->where('role', 'kakanwil')
                ->where(function ($query) use ($kakanwilRoleId) {
                    $query->whereNull('id_role')->orWhere('id_role', '!=', $kakanwilRoleId);
                })
                ->update(['id_role' => $kakanwilRoleId]);
        }

        DB::table('roles')->where('nama_role', 'pimpinan_p3h')->delete();
    }
}
