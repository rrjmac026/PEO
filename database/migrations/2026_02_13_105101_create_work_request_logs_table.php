<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_request_logs', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('work_request_id')
                ->constrained('work_requests', 'id')
                ->cascadeOnDelete()
                ->name('fk_work_request_logs_work_request_id')
                ->index();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees', 'id')
                ->nullOnDelete()
                ->name('fk_work_request_logs_employee_id')
                ->index();

            // Event type (consider enum if you want stricter control)
            $table->string('event')->index();  

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
            $table->index(['work_request_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_request_logs');
    }
};
