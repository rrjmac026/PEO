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
        Schema::create('concrete_pourings', function (Blueprint $table) {
            $table->id();

            // ── Linked Work Request ──────────────────────────────────────────
            // A concrete pouring form is always tied to a work request.
            // nullable() allows standalone forms if needed in the future.
            $table->foreignId('work_request_id')
                ->nullable()
                ->constrained('work_requests')
                ->onDelete('set null');

            // ── Project Information ──────────────────────────────────────────
            // Mirrored from the linked WorkRequest but kept here so the form
            // is self-contained (values may diverge per pour/section).
            $table->string('project_name');
            $table->string('location');
            $table->string('contractor');
            $table->string('part_of_structure');
            $table->decimal('estimated_volume', 10, 2)->comment('In cubic meters');
            $table->string('station_limits_section')->nullable();
            $table->dateTime('pouring_datetime');

            // ── Requested by (Contractor) ────────────────────────────────────
            // Same User who submitted / is named on the linked WorkRequest.
            $table->foreignId('requested_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // ── Checklist Items (20 items from the form) ─────────────────────
            $table->boolean('concrete_vibrator')->default(false);
            $table->boolean('field_density_test')->default(false);
            $table->boolean('protective_covering_materials')->default(false);
            $table->boolean('beam_cylinder_molds')->default(false);
            $table->boolean('warning_signs_barricades')->default(false);
            $table->boolean('curing_materials')->default(false);
            $table->boolean('concrete_saw')->default(false);
            $table->boolean('slump_cones')->default(false);
            $table->boolean('concrete_block_spacer')->default(false);
            $table->boolean('plumbness')->default(false);
            $table->boolean('finishing_tools_equipment')->default(false);
            $table->boolean('quality_of_materials')->default(false);
            $table->boolean('line_grade_alignment')->default(false);
            $table->boolean('lighting_system')->default(false);
            $table->boolean('required_construction_equipment')->default(false);
            $table->boolean('electrical_layout')->default(false);
            $table->boolean('rebar_sizes_spacing')->default(false);
            $table->boolean('plumbing_layout')->default(false);
            $table->boolean('rebars_installation')->default(false);
            $table->boolean('falseworks_formworks')->default(false);

            // ── ME/MTQA Review ───────────────────────────────────────────────
            // Same User as WorkRequest::assigned_mtqa_id on the linked request.
            $table->text('me_mtqa_remarks')->nullable();
            $table->foreignId('me_mtqa_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->date('me_mtqa_date')->nullable();

            // ── Resident Engineer Review ─────────────────────────────────────
            // Same User as WorkRequest::assigned_resident_engineer_id.
            $table->text('re_remarks')->nullable();
            $table->foreignId('resident_engineer_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->date('re_date')->nullable();

            // ── Final Approval / Disapproval ─────────────────────────────────
            // Mirrors the WorkRequest approval flow (approved_by / admin_decision_by).
            $table->enum('status', ['requested', 'approved', 'disapproved'])
                ->default('requested');
            $table->text('approval_remarks')->nullable();

            $table->foreignId('approved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->date('approved_date')->nullable();

            $table->foreignId('disapproved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->date('disapproved_date')->nullable();

            // ── Noted by (Provincial Engineer) ───────────────────────────────
            // Same User as WorkRequest::assigned_provincial_engineer_id.
            $table->foreignId('noted_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->date('noted_date')->nullable();

            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────────────────
            $table->index('work_request_id');
            $table->index('project_name');
            $table->index('location');
            $table->index('contractor');
            $table->index('status');
            $table->index('pouring_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concrete_pourings');
    }
};