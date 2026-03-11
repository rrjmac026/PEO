<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_requests', function (Blueprint $table) {
            // ── Assigned reviewers (set by admin) ─────────────────────────────
            $table->foreignId('assigned_site_inspector_id')
                ->nullable()->after('contractor_name')
                ->constrained('users')->nullOnDelete();

            $table->foreignId('assigned_surveyor_id')
                ->nullable()->after('assigned_site_inspector_id')
                ->constrained('users')->nullOnDelete();

            $table->foreignId('assigned_resident_engineer_id')
                ->nullable()->after('assigned_surveyor_id')
                ->constrained('users')->nullOnDelete();

            $table->foreignId('assigned_mtqa_id')
                ->nullable()->after('assigned_resident_engineer_id')
                ->constrained('users')->nullOnDelete();

            $table->foreignId('assigned_engineer_iv_id')
                ->nullable()->after('assigned_mtqa_id')
                ->constrained('users')->nullOnDelete();

            $table->foreignId('assigned_engineer_iii_id')
                ->nullable()->after('assigned_engineer_iv_id')
                ->constrained('users')->nullOnDelete();

            $table->foreignId('assigned_provincial_engineer_id')
                ->nullable()->after('assigned_engineer_iii_id')
                ->constrained('users')->nullOnDelete();

            // ── Who assigned and when ─────────────────────────────────────────
            $table->foreignId('assigned_by_admin_id')
                ->nullable()->after('assigned_provincial_engineer_id')
                ->constrained('users')->nullOnDelete();

            $table->timestamp('assigned_at')->nullable()->after('assigned_by_admin_id');

            // ── Current review step (tracks whose turn it is) ─────────────────
            // Values: null, 'site_inspector', 'surveyor', 'resident_engineer',
            //         'mtqa', 'engineer_iv', 'engineer_iii', 'provincial_engineer',
            //         'admin_final'
            $table->string('current_review_step')->nullable()->after('assigned_at');

            // ── Admin final decision ──────────────────────────────────────────
            $table->string('admin_decision')->nullable()->after('current_review_step'); // 'approved' | 'rejected'
            $table->text('admin_decision_remarks')->nullable()->after('admin_decision');
            $table->foreignId('admin_decision_by')
                ->nullable()->after('admin_decision_remarks')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('admin_decision_at')->nullable()->after('admin_decision_by');
        });
    }

    public function down(): void
    {
        Schema::table('work_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_site_inspector_id');
            $table->dropConstrainedForeignId('assigned_surveyor_id');
            $table->dropConstrainedForeignId('assigned_resident_engineer_id');
            $table->dropConstrainedForeignId('assigned_mtqa_id');
            $table->dropConstrainedForeignId('assigned_engineer_iv_id');
            $table->dropConstrainedForeignId('assigned_engineer_iii_id');
            $table->dropConstrainedForeignId('assigned_provincial_engineer_id');
            $table->dropConstrainedForeignId('assigned_by_admin_id');
            $table->dropColumn([
                'assigned_at',
                'current_review_step',
                'admin_decision',
                'admin_decision_remarks',
                'admin_decision_at',
            ]);
            $table->dropConstrainedForeignId('admin_decision_by');
        });
    }
};