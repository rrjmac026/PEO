<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcretePouring extends Model
{
    use HasFactory;

    protected $table = 'concrete_pourings';

    protected $fillable = [
        // ── Linked Work Request ──────────────────────────────────────────────
        'work_request_id',

        // ── Project Information ──────────────────────────────────────────────
        // These can be auto-populated from the linked WorkRequest but are kept
        // here so the form remains self-contained (e.g. for standalone pourings
        // or when the values diverge from the original work request).
        'project_name',
        'location',
        'contractor',
        'part_of_structure',
        'estimated_volume',
        'station_limits_section',
        'pouring_datetime',

        // ── Requested by (Contractor) ────────────────────────────────────────
        // Points to the same User that submitted the linked WorkRequest
        // (WorkRequest::contractor_name / assigned_site_inspector_id, etc.)
        // We store a User FK here so we can show a signature and full name.
        'requested_by_user_id',

        // ── Checklist Items ──────────────────────────────────────────────────
        'concrete_vibrator',
        'field_density_test',
        'protective_covering_materials',
        'beam_cylinder_molds',
        'warning_signs_barricades',
        'curing_materials',
        'concrete_saw',
        'slump_cones',
        'concrete_block_spacer',
        'plumbness',
        'finishing_tools_equipment',
        'quality_of_materials',
        'line_grade_alignment',
        'lighting_system',
        'required_construction_equipment',
        'electrical_layout',
        'rebar_sizes_spacing',
        'plumbing_layout',
        'rebars_installation',
        'falseworks_formworks',

        // ── ME/MTQA Review ───────────────────────────────────────────────────
        // Mirrors WorkRequest::assigned_mtqa_id / checked_by_mtqa
        'me_mtqa_user_id',
        'me_mtqa_remarks',
        'me_mtqa_date',

        // ── Resident Engineer Review ─────────────────────────────────────────
        // Mirrors WorkRequest::assigned_resident_engineer_id / resident_engineer_name
        'resident_engineer_user_id',
        're_remarks',
        're_date',

        // ── Final Approval ───────────────────────────────────────────────────
        // Mirrors WorkRequest::approved_by / admin_decision_by
        'status',            // 'requested' | 'approved' | 'disapproved'
        'approval_remarks',
        'approved_by_user_id',
        'approved_date',
        'disapproved_by_user_id',
        'disapproved_date',

        // ── Noted by (Provincial Engineer) ──────────────────────────────────
        // Mirrors WorkRequest::assigned_provincial_engineer_id
        'noted_by_user_id',
        'noted_date',
    ];

    protected $casts = [
        'pouring_datetime'   => 'datetime',
        'me_mtqa_date'       => 'date',
        're_date'            => 'date',
        'approved_date'      => 'date',
        'disapproved_date'   => 'date',
        'noted_date'         => 'date',
        'estimated_volume'   => 'decimal:2',

        // Checklist booleans
        'concrete_vibrator'               => 'boolean',
        'field_density_test'              => 'boolean',
        'protective_covering_materials'   => 'boolean',
        'beam_cylinder_molds'             => 'boolean',
        'warning_signs_barricades'        => 'boolean',
        'curing_materials'                => 'boolean',
        'concrete_saw'                    => 'boolean',
        'slump_cones'                     => 'boolean',
        'concrete_block_spacer'           => 'boolean',
        'plumbness'                       => 'boolean',
        'finishing_tools_equipment'       => 'boolean',
        'quality_of_materials'            => 'boolean',
        'line_grade_alignment'            => 'boolean',
        'lighting_system'                 => 'boolean',
        'required_construction_equipment' => 'boolean',
        'electrical_layout'               => 'boolean',
        'rebar_sizes_spacing'             => 'boolean',
        'plumbing_layout'                 => 'boolean',
        'rebars_installation'             => 'boolean',
        'falseworks_formworks'            => 'boolean',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * The WorkRequest this concrete pouring belongs to.
     * A single work request can have many concrete pouring forms
     * (e.g. one per pour / section).
     */
    public function workRequest()
    {
        return $this->belongsTo(WorkRequest::class, 'work_request_id');
    }

    /**
     * The contractor / requester who filed this form.
     *
     * Resolved from: WorkRequest::contractor_name (name only) or the User
     * who submitted the work request.  Storing the User FK here lets the PDF
     * service render a signature and a full name without extra queries.
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    /**
     * The ME/MTQA reviewer.
     *
     * Same person as WorkRequest::assigned_mtqa_id on the linked work request.
     * You can seed this automatically when creating a ConcretePouring from a
     * WorkRequest (see the static factory helper below).
     */
    public function meMtqaChecker()
    {
        return $this->belongsTo(User::class, 'me_mtqa_user_id');
    }

    /**
     * The Resident Engineer / Project In-Charge reviewer.
     *
     * Same person as WorkRequest::assigned_resident_engineer_id.
     */
    public function residentEngineer()
    {
        return $this->belongsTo(User::class, 'resident_engineer_user_id');
    }

    /**
     * The user who approved this form.
     *
     * Typically the same as WorkRequest::approved_by / admin_decision_by.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    /**
     * The user who disapproved this form.
     */
    public function disapprover()
    {
        return $this->belongsTo(User::class, 'disapproved_by_user_id');
    }

    /**
     * The Provincial Engineer who noted the form.
     *
     * Same person as WorkRequest::assigned_provincial_engineer_id.
     */
    public function notedByEngineer()
    {
        return $this->belongsTo(User::class, 'noted_by_user_id');
    }

    // =========================================================================
    // FACTORY HELPER
    // =========================================================================

    /**
     * Build a new (unsaved) ConcretePouring pre-populated from a WorkRequest.
     *
     * This mirrors the shared personnel across both forms so you don't have to
     * copy user IDs by hand every time a new pouring form is created:
     *
     *   $pouring = ConcretePouring::fromWorkRequest($workRequest);
     *   $pouring->part_of_structure = 'Deck slab — Sta. 12+500';
     *   $pouring->save();
     */
    public static function fromWorkRequest(WorkRequest $wr): static
    {
        return new static([
            'work_request_id'           => $wr->id,

            // Mirror project metadata
            'project_name'              => $wr->name_of_project,
            'location'                  => $wr->project_location,
            'contractor'                => $wr->contractor_name,

            // Mirror the shared reviewers / signatories
            // WorkRequest stores User IDs directly (not via Employee pivot)
            'requested_by_user_id'      => $wr->assigned_by_admin_id,  // or the submitter FK if you track one
            'me_mtqa_user_id'           => $wr->assigned_mtqa_id,
            'resident_engineer_user_id' => $wr->assigned_resident_engineer_id,
            'noted_by_user_id'          => $wr->assigned_provincial_engineer_id,

            'status'                    => 'requested',
        ]);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'requested');
    }

    public function scopeDisapproved($query)
    {
        return $query->where('status', 'disapproved');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('project_name', 'LIKE', "%{$term}%")
              ->orWhere('location',    'LIKE', "%{$term}%")
              ->orWhere('contractor',  'LIKE', "%{$term}%")
              ->orWhere('part_of_structure', 'LIKE', "%{$term}%")
              ->orWhereHas('workRequest', function ($q2) use ($term) {
                  $q2->where('name_of_project',  'LIKE', "%{$term}%")
                     ->orWhere('project_location', 'LIKE', "%{$term}%")
                     ->orWhere('contractor_name',  'LIKE', "%{$term}%");
              })
              ->orWhereHas('requestedBy', function ($q2) use ($term) {
                  $q2->where('name', 'LIKE', "%{$term}%");
              });
        });
    }

    // =========================================================================
    // COMPUTED ATTRIBUTES
    // =========================================================================

    /**
     * Resolve the contractor display name.
     *
     * Priority: explicit `contractor` field → linked WorkRequest contractor →
     * requesting User's name.
     */
    public function getContractorNameAttribute(): string
    {
        return $this->contractor
            ?? $this->workRequest?->contractor_name
            ?? $this->requestedBy?->name
            ?? '';
    }

    public function getProjectTitleAttribute(): string
    {
        return "{$this->project_name} - {$this->location}";
    }

    public function getChecklistCompleteAttribute(): bool
    {
        foreach ($this->checklistFields() as $field) {
            if (!$this->$field) {
                return false;
            }
        }
        return true;
    }

    public function getChecklistProgressAttribute(): float
    {
        $fields    = $this->checklistFields();
        $completed = collect($fields)->filter(fn ($f) => $this->$f)->count();
        return round(($completed / count($fields)) * 100, 2);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'approved'    => 'success',
            'disapproved' => 'danger',
            'requested'   => 'warning',
            default       => 'secondary',
        };
    }

    // =========================================================================
    // ACTION METHODS
    // =========================================================================

    /**
     * Approve the concrete pouring request.
     *
     * @param  User        $approver  The user approving (mirrors WorkRequest approval flow)
     * @param  string|null $remarks
     */
    public function approve(User $approver, ?string $remarks = null): void
    {
        $this->update([
            'status'              => 'approved',
            'approved_by_user_id' => $approver->id,
            'approved_date'       => now(),
            'approval_remarks'    => $remarks,
        ]);
    }

    /**
     * Disapprove the concrete pouring request.
     *
     * @param  User        $disapprover
     * @param  string|null $remarks
     */
    public function disapprove(User $disapprover, ?string $remarks = null): void
    {
        $this->update([
            'status'                 => 'disapproved',
            'disapproved_by_user_id' => $disapprover->id,
            'disapproved_date'       => now(),
            'approval_remarks'       => $remarks,
        ]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function checklistFields(): array
    {
        return [
            'concrete_vibrator',
            'field_density_test',
            'protective_covering_materials',
            'beam_cylinder_molds',
            'warning_signs_barricades',
            'curing_materials',
            'concrete_saw',
            'slump_cones',
            'concrete_block_spacer',
            'plumbness',
            'finishing_tools_equipment',
            'quality_of_materials',
            'line_grade_alignment',
            'lighting_system',
            'required_construction_equipment',
            'electrical_layout',
            'rebar_sizes_spacing',
            'plumbing_layout',
            'rebars_installation',
            'falseworks_formworks',
        ];
    }
}