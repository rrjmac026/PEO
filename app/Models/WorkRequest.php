<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class WorkRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // ── Project Information ──────────────────────────────────────────────
        'reference_number',
        'name_of_project',
        'project_location',

        // ── Addressed To / From ──────────────────────────────────────────────
        'for_office',
        'from_requester',

        // ── Request Details ──────────────────────────────────────────────────
        'requested_work_start_date',
        'requested_work_start_time',

        // ── Pay Item Details ─────────────────────────────────────────────────
        'item_no',
        'description',
        'equipment_to_be_used',
        'quantity',
        'estimated_quantity',
        'unit',
        'description_of_work_requested',

        // ── Submission ───────────────────────────────────────────────────────
        'contractor_name',
        'contractor_signature',

        // ── Assignments (set by admin) ────────────────────────────────────────
        'assigned_site_inspector_id',
        'assigned_surveyor_id',
        'assigned_resident_engineer_id',
        'assigned_mtqa_id',
        'assigned_engineer_iv_id',
        'assigned_engineer_iii_id',
        'assigned_provincial_engineer_id',
        'assigned_by_admin_id',
        'assigned_at',
        'current_review_step',

        // ── Reception ───────────────────────────────────────────────────────
        'received_by',
        'received_date',
        'received_time',

        // ── Inspection: Site Inspector ───────────────────────────────────────
        'inspected_by_site_inspector',
        'site_inspector_signature',
        'findings_comments',
        'recommendation',

        // ── Inspection: Surveyor ─────────────────────────────────────────────
        'surveyor_name',
        'surveyor_signature',
        'findings_surveyor',
        'recommendation_surveyor',

        // ── Inspection: Resident Engineer ────────────────────────────────────
        'resident_engineer_name',
        'resident_engineer_signature',
        'findings_engineer',
        'recommendation_engineer',

        // ── MTQA / Checked By ────────────────────────────────────────────────
        'checked_by_mtqa',
        'mtqa_signature',
        'recommended_action',

        // ── Reviewed By (Engineer IV) ────────────────────────────────────────
        'reviewed_by',
        'reviewer_signature',
        'reviewed_by_recommendation_action',

        // ── Recommending Approval (Engineer III) ─────────────────────────────
        'recommending_approval_by',
        'recommending_approval_signature',
        'recommending_approval_recommendation_action',

        // ── Approved (Provincial Engineer) ───────────────────────────────────
        'approved_by',
        'approved_signature',
        'approved_recommendation_action',

        // ── Admin Final Decision ─────────────────────────────────────────────
        'admin_decision',
        'admin_decision_remarks',
        'admin_decision_by',
        'admin_decision_at',

        // ── Acceptance ───────────────────────────────────────────────────────
        'accepted_by_contractor',
        'accepted_date',
        'accepted_time',

        // ── Status & Notes ───────────────────────────────────────────────────
        'status',
        'notes',
    ];

    protected $casts = [
        'requested_work_start_date' => 'date',
        'accepted_date'             => 'date',
        'received_date'             => 'date',
        'estimated_quantity'        => 'decimal:2',
        'quantity'                  => 'decimal:2',
        'assigned_at'               => 'datetime',
        'admin_decision_at'         => 'datetime',
    ];

    protected $appends = ['submitted_by', 'submitted_date'];

    // ─── Review step order ───────────────────────────────────────────────────
    // Maps step key → assigned_*_id column → role string
    const REVIEW_STEPS = [
        'site_inspector' => [
            'assigned_col' => 'assigned_site_inspector_id',
            'next'         => 'surveyor',
        ],
        'surveyor' => [
            'assigned_col' => 'assigned_surveyor_id',
            'next'         => 'resident_engineer',
        ],
        'resident_engineer' => [
            'assigned_col' => 'assigned_resident_engineer_id',
            'next'         => 'mtqa',
        ],
        'mtqa' => [
            'assigned_col' => 'assigned_mtqa_id',
            'next'         => 'engineer_iv',
        ],
        'engineer_iv' => [
            'assigned_col' => 'assigned_engineer_iv_id',
            'next'         => 'engineer_iii',
        ],
        'engineer_iii' => [
            'assigned_col' => 'assigned_engineer_iii_id',
            'next'         => 'provincial_engineer',
        ],
        'provincial_engineer' => [
            'assigned_col' => 'assigned_provincial_engineer_id',
            'next'         => null,   // <-- FINAL step, no next
        ],
    ];

    // ─── Status constants ────────────────────────────────────────────────────
    const STATUS_DRAFT     = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_ASSIGNED  = 'assigned';      // admin assigned reviewers
    const STATUS_IN_REVIEW = 'in_review';     // engineers are reviewing
    const STATUS_INSPECTED = 'inspected';
    const STATUS_REVIEWED  = 'reviewed';
    const STATUS_APPROVED  = 'approved';
    const STATUS_ACCEPTED  = 'accepted';
    const STATUS_REJECTED  = 'rejected';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_ASSIGNED,
            self::STATUS_IN_REVIEW,
            self::STATUS_INSPECTED,
            self::STATUS_REVIEWED,
            self::STATUS_APPROVED,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function logs()
    {
        return $this->hasMany(WorkRequestLog::class)->orderBy('created_at', 'desc');
    }

    public function assignedSiteInspector()
    {
        return $this->belongsTo(User::class, 'assigned_site_inspector_id');
    }

    public function assignedSurveyor()
    {
        return $this->belongsTo(User::class, 'assigned_surveyor_id');
    }

    public function assignedResidentEngineer()
    {
        return $this->belongsTo(User::class, 'assigned_resident_engineer_id');
    }

    public function assignedMtqa()
    {
        return $this->belongsTo(User::class, 'assigned_mtqa_id');
    }

    public function assignedEngineerIv()
    {
        return $this->belongsTo(User::class, 'assigned_engineer_iv_id');
    }

    public function assignedEngineerIii()
    {
        return $this->belongsTo(User::class, 'assigned_engineer_iii_id');
    }

    public function assignedProvincialEngineer()
    {
        return $this->belongsTo(User::class, 'assigned_provincial_engineer_id');
    }

    public function assignedByAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_by_admin_id');
    }

    public function adminDecisionBy()
    {
        return $this->belongsTo(User::class, 'admin_decision_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePendingApproval($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SUBMITTED,
            self::STATUS_ASSIGNED,
            self::STATUS_IN_REVIEW,
            self::STATUS_INSPECTED,
            self::STATUS_REVIEWED,
        ]);
    }

    /**
     * Scope: requests where the given user is the current assigned reviewer.
     */
    public function scopeAssignedToUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'site_inspector')
                   ->where('assigned_site_inspector_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'surveyor')
                   ->where('assigned_surveyor_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'resident_engineer')
                   ->where('assigned_resident_engineer_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'mtqa')
                   ->where('assigned_mtqa_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'engineer_iv')
                   ->where('assigned_engineer_iv_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'engineer_iii')
                   ->where('assigned_engineer_iii_id', $userId);
            })->orWhere(function ($q2) use ($userId) {
                $q2->where('current_review_step', 'provincial_engineer')
                   ->where('assigned_provincial_engineer_id', $userId);
            });
        });
    }

    // ─── Predicates ──────────────────────────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canEdit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SUBMITTED]);
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_by_admin_id);
    }

    /**
     * Check if the given user is the current assigned reviewer.
     */
    public function isCurrentReviewer(User $user): bool
    {
        $step = $this->current_review_step;

        if (!$step || !isset(self::REVIEW_STEPS[$step])) {
            return false;
        }

        $col = self::REVIEW_STEPS[$step]['assigned_col'];

        if (!$col) {
            return false; // admin_final — handled separately
        }

        return $this->$col == $user->id;
    }

    /**
     * Advance to the next review step.
     * Skips steps where no engineer was assigned.
     * Returns true if advanced, false if we've reached admin_final.
     */
    public function advanceReviewStep(): void
    {
        $currentStep = $this->current_review_step;

        if (! $currentStep || ! isset(self::REVIEW_STEPS[$currentStep])) {
            return;
        }

        $next = self::REVIEW_STEPS[$currentStep]['next'];

        // Walk forward, skipping unassigned steps
        while ($next !== null) {
            $col = self::REVIEW_STEPS[$next]['assigned_col'] ?? null;
            if ($col && ! is_null($this->$col)) {
                break; // found an assigned step
            }
            $next = self::REVIEW_STEPS[$next]['next'] ?? null;
        }

        if ($next === null) {
            // No more assigned reviewer steps found after current step.
            // Check if provincial engineer is assigned — if yes, route there.
            // If not, route back to admin for further assignment (status = assigned).
            if (! is_null($this->assigned_provincial_engineer_id)) {
                $this->update([
                    'current_review_step' => 'provincial_engineer',
                    'status'              => self::STATUS_IN_REVIEW,
                ]);
            } else {
                // Return to admin queue so they can assign remaining reviewers
                $this->update([
                    'current_review_step' => null,
                    'status'              => self::STATUS_ASSIGNED,
                ]);
            }
        } else {
            $this->update([
                'current_review_step' => $next,
                'status'              => self::STATUS_IN_REVIEW,
            ]);
        }
    }

    /**
     * Get human-readable label for current_review_step.
     */
    public function getCurrentStepLabelAttribute(): string
    {
        return match ($this->current_review_step) {
            'site_inspector'      => 'Site Inspector',
            'surveyor'            => 'Surveyor',
            'resident_engineer'   => 'Resident Engineer',
            'mtqa'                => 'MTQA',
            'engineer_iv'         => 'Engineer IV',
            'engineer_iii'        => 'Engineer III',
            'provincial_engineer' => 'Provincial Engineer (Final Decision)',
            null                  => 'Complete',
            default               => ucfirst(str_replace('_', ' ', $this->current_review_step)),
        };
    }

    /**
     * Get the User assigned to the current step (for display).
     */
    public function getCurrentReviewerAttribute(): ?User
    {
        $step = $this->current_review_step;

        if (!$step || !isset(self::REVIEW_STEPS[$step])) {
            return null;
        }

        $col = self::REVIEW_STEPS[$step]['assigned_col'];

        return $col ? $this->$col()->first() : null;
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getProjectNameAttribute(): string
    {
        return $this->name_of_project ?? 'Untitled Project';
    }

    public function getSubmittedByAttribute(): ?string
    {
        return $this->contractor_name;
    }

    public function getSubmittedDateAttribute(): ?\Carbon\Carbon
    {
        return $this->created_at;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT      => 'gray',
            self::STATUS_SUBMITTED  => 'blue',
            self::STATUS_ASSIGNED   => 'indigo',
            self::STATUS_IN_REVIEW  => 'yellow',
            self::STATUS_INSPECTED  => 'cyan',
            self::STATUS_REVIEWED   => 'purple',
            self::STATUS_APPROVED   => 'green',
            self::STATUS_ACCEPTED   => 'teal',
            self::STATUS_REJECTED   => 'red',
            default                 => 'gray',
        };
    }

    // ─── Validation ──────────────────────────────────────────────────────────

    public static function validationRules($id = null): array
    {
        return [
            'reference_number'                            => 'nullable|string|max:255',
            'name_of_project'                             => 'required|string|max:255',
            'project_location'                            => 'required|string|max:255',
            'for_office'                                  => 'nullable|string|max:255',
            'from_requester'                              => 'nullable|string|max:255',
            'requested_work_start_date'                   => 'required|date',
            'requested_work_start_time'                   => 'nullable|string|max:20',
            'item_no'                                     => 'nullable|string|max:100',
            'description'                                 => 'nullable|string|max:255',
            'equipment_to_be_used'                        => 'nullable|string|max:255',
            'estimated_quantity'                          => 'nullable|numeric|min:0',
            'quantity'                                    => 'nullable|numeric|min:0',
            'unit'                                        => 'nullable|string|max:50',
            'description_of_work_requested'               => 'required|string',
            'contractor_name'                             => 'nullable|string|max:255',
            'received_by'                                 => 'nullable|string|max:255',
            'received_date'                               => 'nullable|date',
            'received_time'                               => 'nullable|string|max:20',
            'inspected_by_site_inspector'                 => 'nullable|string|max:255',
            'site_inspector_signature'                    => 'nullable|string',
            'findings_comments'                           => 'nullable|string',
            'recommendation'                              => 'nullable|string',
            'surveyor_name'                               => 'nullable|string|max:255',
            'surveyor_signature'                          => 'nullable|string',
            'findings_surveyor'                           => 'nullable|string',
            'recommendation_surveyor'                     => 'nullable|string',
            'resident_engineer_name'                      => 'nullable|string|max:255',
            'resident_engineer_signature'                 => 'nullable|string',
            'findings_engineer'                           => 'nullable|string',
            'recommendation_engineer'                     => 'nullable|string',
            'checked_by_mtqa'                             => 'nullable|string|max:255',
            'mtqa_signature'                              => 'nullable|string',
            'recommended_action'                          => 'nullable|string',
            'reviewed_by'                                 => 'nullable|string|max:255',
            'reviewer_signature'                          => 'nullable|string',
            'reviewed_by_recommendation_action'           => 'nullable|string',
            'recommending_approval_by'                    => 'nullable|string|max:255',
            'recommending_approval_signature'             => 'nullable|string',
            'recommending_approval_recommendation_action' => 'nullable|string',
            'approved_by'                                 => 'nullable|string|max:255',
            'approved_signature'                          => 'nullable|string',
            'approved_recommendation_action'              => 'nullable|string',
            'accepted_by_contractor'                      => 'nullable|string|max:255',
            'accepted_date'                               => 'nullable|date',
            'accepted_time'                               => 'nullable|string|max:20',
            'status'  => 'nullable|string|in:' . implode(',', self::getStatuses()),
            'notes'   => 'nullable|string',
        ];
    }

    // ─── Logging helpers ─────────────────────────────────────────────────────

    public function addLog(string $event, array $data = []): WorkRequestLog
    {
        return $this->logs()->create([
            'event'       => $event,
            'user_id'     => $data['user_id'] ?? Auth::id(),
            'description' => $data['description'] ?? null,
            'note'        => $data['note'] ?? null,
            'changes'     => $data['changes'] ?? null,
            'status_from' => $data['status_from'] ?? null,
            'status_to'   => $data['status_to'] ?? null,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }

    public function buildChanges(array $newData): array
    {
        $changes = [];
        foreach ($newData as $field => $newValue) {
            $oldValue = $this->getOriginal($field);
            if ((string) $oldValue !== (string) $newValue) {
                $changes[$field] = [$oldValue, $newValue];
            }
        }
        return $changes;
    }
}