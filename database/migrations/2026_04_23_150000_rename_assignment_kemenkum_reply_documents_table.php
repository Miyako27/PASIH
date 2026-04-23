<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assignment_kemenkum_reply_documents') && ! Schema::hasTable('suratbalasan_documents')) {
            Schema::rename('assignment_kemenkum_reply_documents', 'suratbalasan_documents');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('suratbalasan_documents') && ! Schema::hasTable('assignment_kemenkum_reply_documents')) {
            Schema::rename('suratbalasan_documents', 'assignment_kemenkum_reply_documents');
        }
    }
};
