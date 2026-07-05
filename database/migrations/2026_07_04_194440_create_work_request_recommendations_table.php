<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_request_recommendations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('work_request_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Which pipeline step this recommendation belongs to
            // (site_inspector, surveyor, resident_engineer, mtqa, engineer_iv, engineer_iii, provincial_engineer)
            $table->string('step');

            // Who wrote it
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->text('recommendation_text');

            // Nullable — null means "sent back for revision, not yet signed"
            $table->longText('signature')->nullable();

            // Convenience flag so we don't have to keep checking !is_null(signature)
            $table->boolean('is_signed')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_request_recommendations');
    }
};