<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_backups', function (Blueprint $table) {
            $table->id();
            $table->string('backup_name');
            $table->string('file_path')->nullable();
            $table->enum('backup_type', ['database', 'full'])->default('database');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('backup_date')->nullable();
            $table->timestamp('retention_until')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_backups');
    }
};