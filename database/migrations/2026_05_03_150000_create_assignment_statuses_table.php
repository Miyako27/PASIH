<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->timestamps();
        });

        DB::table('assignments')
            ->select(['id', 'assigned_by_id', 'status', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                $inserts = [];
                foreach ($rows as $row) {
                    $inserts[] = [
                        'assignment_id' => $row->id,
                        'user_id' => $row->assigned_by_id,
                        'status' => (string) $row->status,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ];
                }

                if ($inserts !== []) {
                    DB::table('assignment_statuses')->insert($inserts);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_statuses');
    }
};
