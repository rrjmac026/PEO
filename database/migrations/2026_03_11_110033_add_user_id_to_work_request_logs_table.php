<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_request_logs', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('users', 'id')
                ->nullOnDelete();
        });

        // Backfill: if a log's employee has a user, copy that user_id over
        DB::statement('
            UPDATE work_request_logs wrl
            JOIN employees e ON e.id = wrl.employee_id
            SET wrl.user_id = e.user_id
            WHERE wrl.user_id IS NULL AND wrl.employee_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('work_request_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};