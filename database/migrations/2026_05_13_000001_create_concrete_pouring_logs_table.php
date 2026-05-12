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

            // Relationships
            $table->foreignId('concrete_pouring_id')
                ->constrained('concrete_pourings', 'id')
                ->cascadeOnDelete()
                ->name('fk_concrete_pouring_logs_concrete_pouring_id')
                ->index();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete()
                ->name('fk_concrete_pouring_logs_user_id')
                ->index();

            // Event type
            $table->string('event')->index();

            // Review step tracking
            $table->string('review_step')->nullable()->index();

            // Status tracking
            $table->string('status_from')->nullable()->index();
            $table->string('status_to')->nullable()->index();

            // Description & change snapshot
            $table->text('description')->nullable();
            $table->json('changes')->nullable();
            $table->text('note')->nullable();

            // Audit trail
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Optional composite index for faster filtering
            $table->index(['concrete_pouring_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concrete_pouring_logs');
    }
};
