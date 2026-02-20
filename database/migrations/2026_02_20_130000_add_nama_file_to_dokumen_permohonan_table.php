<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_permohonan', function (Blueprint $table) {
            if (! Schema::hasColumn('dokumen_permohonan', 'nama_file')) {
                $table->string('nama_file', 255)->nullable()->after('id_user_upload');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_permohonan', function (Blueprint $table) {
            if (Schema::hasColumn('dokumen_permohonan', 'nama_file')) {
                $table->dropColumn('nama_file');
            }
        });
    }
};

