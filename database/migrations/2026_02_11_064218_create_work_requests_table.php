<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_requests', function (Blueprint $table) {
            $table->id();

            // Project Information
            $table->string('name_of_project')->nullable();
            $table->string('project_location')->nullable();

            // For and From (addressed to/from)
            $table->string('for_office')->nullable();
            $table->string('from_requester')->nullable();

            // Request Details
            $table->string('requested_by')->nullable();
            $table->date('requested_work_start_date')->nullable();
            $table->time('requested_work_start_time')->nullable();

            // Pay Item Details
            $table->string('item_no')->nullable();
            $table->text('description')->nullable();
            $table->string('equipment_to_be_used')->nullable();
            $table->decimal('estimated_quantity', 8, 2)->nullable();
            $table->string('unit')->nullable();
            $table->text('description_of_work_requested')->nullable();

            // Submission
            $table->string('submitted_by')->nullable();
            $table->date('submitted_date')->nullable();
            $table->string('contractor_name')->nullable();

            // Inspection
            $table->string('inspected_by_site_inspector')->nullable();
            $table->text('site_inspector_signature')->nullable();
            $table->string('surveyor_name')->nullable();
            $table->text('surveyor_signature')->nullable();
            $table->string('resident_engineer_name')->nullable();
            $table->text('resident_engineer_signature')->nullable();

            // Findings and Recommendations
            $table->text('findings_comments')->nullable();
            $table->text('recommendation')->nullable();
            $table->text('recommended_action')->nullable();

            // Review and Approval
            $table->string('checked_by_mtqa')->nullable();
            $table->text('mtqa_signature')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->string('reviewer_designation')->nullable();
            $table->string('recommending_approval_by')->nullable();
            $table->string('recommending_approval_designation')->nullable();
            $table->text('recommending_approval_signature')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('approved_by_designation')->nullable();
            $table->text('approved_signature')->nullable();

            // Acceptance
            $table->string('accepted_by_contractor')->nullable();
            $table->date('accepted_date')->nullable();
            $table->time('accepted_time')->nullable();
            $table->string('received_by')->nullable();
            $table->date('received_date')->nullable();
            $table->time('received_time')->nullable();

            // Status
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_requests');
    }
};
