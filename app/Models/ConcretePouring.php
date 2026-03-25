<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcretePouring extends Model
{
    use HasFactory;

    protected $table = 'concrete_pourings';

    /**
     * Auto-generate a reference number on creation if one is not supplied.
     * Format: CP-YYYY-NNNN  (e.g. CP-2025-0001)
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->reference_number)) {
                $year  = now()->format('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $model->reference_number = sprintf('CP-%s-%04d', $year, $count);
            }
        });
    }

    protected $fillable = [
        // ── Linked Work Request ──────────────────────────────────────────────
        'work_request_id',

        // ── Reference Number ─────────────────────────────────────────────────
        'reference_number',

        // ── Project Information ──────────────────────────────────────────────
        'project_name',
        'location',
        'contractor',
        'part_of_structure',
        'estimated_volume',
        'station_limits_section',
        'pouring_datetime',

        // ── Requested by (Contractor) ────────────────────────────────────────
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

        // ── Review Pipeline ──────────────────────────────────────────────────
        'current_review_step',
        'assigned_by_admin_id',
        'assigned_at',

        // ── ME/MTQA Review ───────────────────────────────────────────────────
        'me_mtqa_user_id',
        'me_mtqa_remarks',
        'me_mtqa_date',
        'me_mtqa_signature',          // ← NEW

        // ── Resident Engineer Review ─────────────────────────────────────────
        'resident_engineer_user_id',
        're_remarks',
        're_date',
        're_signature',               // ← NEW

        // ── Noted by (Provincial Engineer) ──────────────────────────────────
        'noted_by_user_id',
        'noted_date',
        'noted_by_signature',         // ← NEW

        // ── Final Approval ───────────────────────────────────────────────────
        'status',
        'approval_remarks',
        'approved_by_user_id',
        'approved_date',
        'disapproved_by_user_id',
        'disapproved_date',
    ];

    protected $casts = [
        'pouring_datetime'  => 'datetime',
        'assigned_at'       => 'datetime',
        'me_mtqa_date'      => 'date',
        're_date'           => 'date',
        'approved_date'     => 'date',
        'disapproved_date'  => 'date',
        'noted_date'        => 'date',
        'estimated_volume'  => 'decimal:2',

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

    public function workRequest()
    {
        return $this->belongsTo(WorkRequest::class, 'work_request_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function assignedByAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_by_admin_id');
    }

    public function meMtqaChecker()
    {
        return $this->belongsTo(User::class, 'me_mtqa_user_id');
    }

    public function residentEngineer()
    {
        return $this->belongsTo(User::class, 'resident_engineer_user_id');
    }

    public function notedByEngineer()
    {
        return $this->belongsTo(User::class, 'noted_by_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function disapprover()
    {
        return $this->belongsTo(User::class, 'disapproved_by_user_id');
    }

    // =========================================================================
    // FACTORY HELPER
    // =========================================================================

    public static function fromWorkRequest(WorkRequest $wr): static
    {
        return new static([
            'work_request_id'           => $wr->id,
            'project_name'              => $wr->name_of_project,
            'location'                  => $wr->project_location,
            'contractor'                => $wr->contractor_name,
            'requested_by_user_id'      => $wr->assigned_by_admin_id,
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
            $q->where('reference_number',  'LIKE', "%{$term}%")
              ->orWhere('project_name',       'LIKE', "%{$term}%")
              ->orWhere('location',          'LIKE', "%{$term}%")
              ->orWhere('contractor',        'LIKE', "%{$term}%")
              ->orWhere('part_of_structure', 'LIKE', "%{$term}%")
              ->orWhereHas('workRequest', function ($q2) use ($term) {
                  $q2->where('name_of_project',   'LIKE', "%{$term}%")
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

    public function getCurrentStepLabelAttribute(): string
    {
        return match ($this->current_review_step) {
            'mtqa'                => 'ME/MTQA',
            'resident_engineer'   => 'Resident Engineer',
            'provincial_engineer' => 'Provincial Engineer',
            'admin_final'         => 'Admin Final Decision',
            default               => 'Pending Assignment',
        };
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

    /**
     * Resolve a stored signature value to a display URL.
     * Handles: base64 data URIs, full URLs, and storage-relative paths.
     */
    public function resolveSignatureUrl(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        if (str_starts_with($value, 'data:image')) {
            return $value;
        }
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        // Treat as storage-relative path
        return asset('storage/' . ltrim($value, '/'));
    }

    // =========================================================================
    // ACTION METHODS
    // =========================================================================

    public function approve(User $approver, ?string $remarks = null): void
    {
        $this->update([
            'status'              => 'approved',
            'approved_by_user_id' => $approver->id,
            'approved_date'       => now(),
            'approval_remarks'    => $remarks,
        ]);
    }

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
            'concrete_vibrator', 'field_density_test', 'protective_covering_materials',
            'beam_cylinder_molds', 'warning_signs_barricades', 'curing_materials',
            'concrete_saw', 'slump_cones', 'concrete_block_spacer', 'plumbness',
            'finishing_tools_equipment', 'quality_of_materials', 'line_grade_alignment',
            'lighting_system', 'required_construction_equipment', 'electrical_layout',
            'rebar_sizes_spacing', 'plumbing_layout', 'rebars_installation',
            'falseworks_formworks',
        ];
    }
}