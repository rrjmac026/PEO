<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_requests', function (Blueprint $table) {
            $table->id();

            // ── Project Information ──────────────────────────────────
            $table->string('reference_number')->nullable();
            $table->string('name_of_project')->nullable();
            $table->string('project_location')->nullable();

            // ── Addressed To / From ──────────────────────────────────
            $table->string('for_office')->nullable();
            $table->string('from_requester')->nullable();

            // ── Request Details ──────────────────────────────────────
            $table->date('requested_work_start_date')->nullable();
            $table->time('requested_work_start_time')->nullable();

            // ── Pay Item Details ─────────────────────────────────────
            $table->string('item_no')->nullable();
            $table->text('description')->nullable();
            $table->string('equipment_to_be_used')->nullable();
            $table->decimal('estimated_quantity', 8, 2)->nullable();
            $table->decimal('quantity', 8, 2)->nullable();
            $table->string('unit')->nullable();
            $table->text('description_of_work_requested')->nullable();

            // ── Submission ───────────────────────────────────────────
            $table->string('contractor_name')->nullable();

            // ── Reception ───────────────────────────────────────────
            $table->string('received_by')->nullable();
            $table->date('received_date')->nullable();
            $table->time('received_time')->nullable();

            // ── Inspection: Site Inspector ───────────────────────────
            $table->string('inspected_by_site_inspector')->nullable();
            $table->text('site_inspector_signature')->nullable();
            $table->text('findings_comments')->nullable();
            $table->text('recommendation')->nullable();

            // ── Inspection: Surveyor ─────────────────────────────────
            $table->string('surveyor_name')->nullable();
            $table->text('surveyor_signature')->nullable();
            $table->text('findings_surveyor')->nullable();
            $table->text('recommendation_surveyor')->nullable();

            // ── Inspection: Resident Engineer ────────────────────────
            $table->string('resident_engineer_name')->nullable();
            $table->text('resident_engineer_signature')->nullable();
            $table->text('findings_engineer')->nullable();
            $table->text('recommendation_engineer')->nullable();

            // ── MTQA / Checked By ────────────────────────────────────
            $table->string('checked_by_mtqa')->nullable();
            $table->text('mtqa_signature')->nullable();
            $table->text('recommended_action')->nullable();

            // ── Reviewed By (Engineer IV) ────────────────────────────
            $table->string('reviewed_by')->nullable();
            $table->text('reviewer_signature')->nullable();
            $table->text('reviewed_by_recommendation_action')->nullable();

            // ── Recommending Approval (Engineer III) ─────────────────
            $table->string('recommending_approval_by')->nullable();
            $table->text('recommending_approval_signature')->nullable();
            $table->text('recommending_approval_recommendation_action')->nullable();

            // ── Approved (Provincial Engineer) ───────────────────────
            $table->string('approved_by')->nullable();
            $table->text('approved_signature')->nullable();
            $table->text('approved_recommendation_action')->nullable();

            // ── Acceptance ───────────────────────────────────────────
            $table->string('accepted_by_contractor')->nullable();
            $table->date('accepted_date')->nullable();
            $table->time('accepted_time')->nullable();

            // ── Status & Notes ───────────────────────────────────────
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_requests');
    }
};