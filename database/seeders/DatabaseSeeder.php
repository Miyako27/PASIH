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
        $password = Hash::make('password');
        $now = now();

        $roleMap = Role::query()->pluck('id_role', 'nama_role');

        $instansiPemda = Instansi::query()->firstOrCreate(
            ['nama_instansi' => 'Biro Hukum Pemerintah Daerah'],
            ['jenis_instansi' => 'Pemerintah Daerah', 'alamat' => 'Alamat Pemda', 'created_at' => $now, 'updated_at' => $now]
        );

        $instansiKanwil = Instansi::query()->firstOrCreate(
            ['nama_instansi' => 'Kanwil Kemenkum'],
            ['jenis_instansi' => 'Kemenkum', 'alamat' => 'Alamat Kanwil', 'created_at' => $now, 'updated_at' => $now]
        );

        $instansiP3H = Instansi::query()->firstOrCreate(
            ['nama_instansi' => 'Divisi P3H'],
            ['jenis_instansi' => 'Kemenkum', 'alamat' => 'Alamat Divisi P3H', 'created_at' => $now, 'updated_at' => $now]
        );

        User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@pasih.test',
            'password' => $password,
            'role' => 'admin',
            'id_role' => $roleMap['admin'] ?? null,
            'id_instansi' => $instansiKanwil->id_instansi,
        ]);

        User::query()->create([
            'name' => 'Operator Pemda',
            'email' => 'pemda@pasih.test',
            'password' => $password,
            'role' => 'operator_pemda',
            'id_role' => $roleMap['operator_pemda'] ?? null,
            'id_instansi' => $instansiPemda->id_instansi,
        ]);

        User::query()->create([
            'name' => 'Operator Kanwil',
            'email' => 'kanwil@pasih.test',
            'password' => $password,
            'role' => 'operator_kanwil',
            'id_role' => $roleMap['operator_kanwil'] ?? null,
            'id_instansi' => $instansiKanwil->id_instansi,
        ]);

        User::query()->create([
            'name' => 'Operator Divisi P3H',
            'email' => 'divisi@pasih.test',
            'password' => $password,
            'role' => 'operator_divisi_p3h',
            'id_role' => $roleMap['operator_divisi_p3h'] ?? null,
            'id_instansi' => $instansiP3H->id_instansi,
        ]);

        User::query()->create([
            'name' => 'Kakanwil',
            'email' => 'kakanwil@pasih.test',
            'password' => $password,
            'role' => 'kakanwil',
            'id_role' => $roleMap['kakanwil'] ?? null,
            'id_instansi' => $instansiKanwil->id_instansi,
        ]);

        User::query()->create([
            'name' => 'Kepala Divisi P3H',
            'email' => 'kadivp3h@pasih.test',
            'password' => $password,
            'role' => 'kepala_divisi_p3h',
            'id_role' => $roleMap['kepala_divisi_p3h'] ?? null,
            'id_instansi' => $instansiP3H->id_instansi,
        ]);

        User::query()->create([
            'name' => 'Analis Hukum',
            'email' => 'analis@pasih.test',
            'password' => $password,
            'role' => 'analis_hukum',
            'id_role' => $roleMap['analis_hukum'] ?? null,
            'id_instansi' => $instansiP3H->id_instansi,
        ]);
    }
}
