<?php

namespace Database\Seeders;

use App\Models\Instansi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        $instansiKanwil = Instansi::query()->firstOrCreate(
            ['nama_instansi' => 'Kanwil Kemenkum'],
            ['jenis_instansi' => 'Kemenkum', 'alamat' => 'Alamat Kanwil', 'created_at' => $now, 'updated_at' => $now]
        );

        $adminRole = Role::query()->firstOrCreate(['nama_role' => 'admin']);

        User::query()->updateOrCreate([
            'email' => 'admin2@pasih.test',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'id_role' => $adminRole->id_role,
            'id_instansi' => $instansiKanwil->id_instansi,
        ]);
    }
}
