<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->id();

            // ── Reference ─────────────────────────────────────────────────────
            $table->string('reference_number')->nullable()->unique()
                ->comment('e.g. MEMO-2026-0001 — auto-generated');

            // ── Type & Subject ────────────────────────────────────────────────
            $table->enum('type', [
                'announcement',
                'birthday',
                'holiday_greeting',
                'policy_update',
                'event_invitation',
                'performance_notice',
                'general',
            ])->default('general');

            $table->string('subject');
            $table->longText('body');          // HTML or plain text content

            // ── Sender ────────────────────────────────────────────────────────
            $table->foreignId('sent_by_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ── Scheduling ────────────────────────────────────────────────────
            $table->enum('status', ['draft', 'scheduled', 'sent', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable()
                ->comment('NULL = send immediately on publish');
            $table->timestamp('sent_at')->nullable();

            // ── Recipients targeting ──────────────────────────────────────────
            $table->enum('recipient_scope', ['all', 'by_role', 'by_department', 'specific'])
                ->default('specific');
            $table->json('target_roles')->nullable()
                ->comment('Array of role strings when scope = by_role');
            $table->json('target_departments')->nullable()
                ->comment('Array of department strings when scope = by_department');

            // ── Attachments ───────────────────────────────────────────────────
            $table->json('attachments')->nullable()
                ->comment('Array of stored file paths');

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('type');
            $table->index('scheduled_at');
        });

        Schema::create('memo_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memo_id')->constrained('memos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamp('read_at')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->boolean('email_failed')->default(false);

            $table->timestamps();

            $table->unique(['memo_id', 'user_id']);
            $table->index(['memo_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_recipients');
        Schema::dropIfExists('memos');
    }
};