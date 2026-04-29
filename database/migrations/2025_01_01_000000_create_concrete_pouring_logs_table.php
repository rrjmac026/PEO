<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concrete_pouring_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('concrete_pouring_id')
                  ->constrained('concrete_pourings')
                  ->cascadeOnDelete();

            // Who performed the action (nullable for system events)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Event key — matches ConcretePouringLog::EVENT_* constants
            $table->string('event', 50);

            // Human-readable description
            $table->text('description')->nullable();

            // Optional freeform note
            $table->text('note')->nullable();

            // JSON diff: ['field' => [old, new], ...]
            $table->json('changes')->nullable();

            // Snapshot of the review pipeline step at the time of the event
            $table->string('review_step', 50)->nullable();

            // Status transition
            $table->string('status_from', 50)->nullable();
            $table->string('status_to',   50)->nullable();

            // Request metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concrete_pouring_logs');
    }
};
