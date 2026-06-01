<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concrete_pouring_checklist_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concrete_pouring_id')
                ->constrained('concrete_pourings')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->string('field');        // e.g. 'concrete_vibrator'
            $table->boolean('checked');     // true = checked, false = unchecked
            $table->timestamps();

            $table->index(['concrete_pouring_id', 'field']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concrete_pouring_checklist_logs');
    }
};