<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id_role');
            $table->string('nama_role', 100)->unique();
            $table->timestamps();
        });

        Schema::create('instansi', function (Blueprint $table) {
            $table->bigIncrements('id_instansi');
            $table->string('nama_instansi', 150);
            $table->string('jenis_instansi', 100);
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('id_role')->nullable()->after('role');
            $table->foreignId('id_instansi')->nullable()->after('id_role');

            $table->foreign('id_role')->references('id_role')->on('roles')->nullOnDelete();
            $table->foreign('id_instansi')->references('id_instansi')->on('instansi')->nullOnDelete();
        });

        Schema::create('permohonan', function (Blueprint $table) {
            $table->bigIncrements('id_permohonan');
            $table->string('nomor_surat', 100)->unique();
            $table->date('tanggal_pengajuan');
            $table->string('perihal', 200);
            $table->text('deskripsi')->nullable();
            $table->foreignId('id_pengaju')->constrained('users')->cascadeOnDelete();
            $table->string('status_permohonan', 50)->default('diajukan');
            $table->text('catatan_revisi')->nullable();
            $table->timestamps();
        });

        Schema::create('dokumen_permohonan', function (Blueprint $table) {
            $table->bigIncrements('id_dokumen_permohonan');
            $table->foreignId('id_permohonan')->references('id_permohonan')->on('permohonan')->cascadeOnDelete();
            $table->foreignId('id_user_upload')->constrained('users')->cascadeOnDelete();
            $table->string('nama_file', 255);
            $table->string('path_file', 255);
            $table->timestamp('tanggal_upload')->useCurrent();
        });

        Schema::create('disposisi', function (Blueprint $table) {
            $table->bigIncrements('id_disposisi');
            $table->foreignId('id_permohonan')->references('id_permohonan')->on('permohonan')->cascadeOnDelete();
            $table->foreignId('dari_user')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ke_instansi')->references('id_instansi')->on('instansi')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_disposisi')->useCurrent();
        });

        Schema::create('penugasan', function (Blueprint $table) {
            $table->bigIncrements('id_penugasan');
            $table->foreignId('id_permohonan')->references('id_permohonan')->on('permohonan')->cascadeOnDelete();
            $table->text('catatan_penugasan')->nullable();
            $table->date('batas_waktu')->nullable();
            $table->string('status_penugasan', 50)->default('ditugaskan');
            $table->foreignId('diambil_oleh')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->foreignId('ditugaskan_oleh')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamp('tanggal_penugasan')->useCurrent();
            $table->timestamp('tanggal_diambil')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
        });

        Schema::create('hasil_analisis', function (Blueprint $table) {
            $table->bigIncrements('id_hasil');
            $table->foreignId('id_penugasan')->references('id_penugasan')->on('penugasan')->cascadeOnDelete();
            $table->foreignId('id_analis')->constrained('users')->cascadeOnDelete();
            $table->text('ringkasan_analisis')->nullable();
            $table->text('hasil_evaluasi')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
        });

        Schema::create('dokumen_hasil', function (Blueprint $table) {
            $table->bigIncrements('id_dokumen_hasil');
            $table->foreignId('id_hasil')->references('id_hasil')->on('hasil_analisis')->cascadeOnDelete();
            $table->foreignId('id_user_upload')->constrained('users')->cascadeOnDelete();
            $table->string('nama_file', 255);
            $table->string('path_file', 255);
            $table->timestamp('tanggal_upload')->useCurrent();
        });

        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->bigIncrements('id_riwayat');
            $table->foreignId('id_permohonan')->references('id_permohonan')->on('permohonan')->cascadeOnDelete();
            $table->string('status', 50);
            $table->foreignId('diubah_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamp('tanggal_perubahan')->useCurrent();
            $table->text('catatan')->nullable();
        });

        DB::table('roles')->insert([
            ['nama_role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'operator_pemda', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'operator_kanwil', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'operator_divisi_p3h', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'kakanwil', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'kepala_divisi_p3h', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'analis_hukum', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
        Schema::dropIfExists('dokumen_hasil');
        Schema::dropIfExists('hasil_analisis');
        Schema::dropIfExists('penugasan');
        Schema::dropIfExists('disposisi');
        Schema::dropIfExists('dokumen_permohonan');
        Schema::dropIfExists('permohonan');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_role']);
            $table->dropForeign(['id_instansi']);
            $table->dropColumn(['id_role', 'id_instansi']);
        });

        Schema::dropIfExists('instansi');
        Schema::dropIfExists('roles');
    }
};
