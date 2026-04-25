<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guide_documents', function (Blueprint $table) {
            $table->string('document_title', 150)->default('')->after('file_name');
        });

        DB::table('guide_documents')
            ->where('document_title', '')
            ->update([
                'document_title' => DB::raw('file_name'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_documents', function (Blueprint $table) {
            $table->dropColumn('document_title');
        });
    }
};

