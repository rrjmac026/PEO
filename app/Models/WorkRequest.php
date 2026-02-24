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
        // ── Project Information ──────────────────────────────────────
        'reference_number',//
        'name_of_project',//
        'project_location',//

        // ── Addressed To / From ──────────────────────────────────────
        'for_office',//
        'from_requester',//

        // ── Request Details ──────────────────────────────────────────
        'requested_work_start_date',//
        'requested_work_start_time',//

        // ── Pay Item Details ─────────────────────────────────────────
        'item_no',//
        'description',//
        'equipment_to_be_used',//
        'quantity',//
        'estimated_quantity',//
        'unit',//
        'description_of_work_requested',

        // ── Submission ───────────────────────────────────────────────
        'contractor_name',// Contractor name (replaces submitted_by)

        // ── Reception ───────────────────────────────────────────────
        'received_by',//
        'received_date',//
        'received_time',//

        // ── Inspection: Site Inspector ───────────────────────────────
        'inspected_by_site_inspector',//
        'site_inspector_signature',//
        'findings_comments',//
        'recommendation',//

        // ── Inspection: Surveyor ─────────────────────────────────────
        'surveyor_name',//
        'surveyor_signature',//
        'findings_surveyor',//
        'recommendation_surveyor',//

        // ── Inspection: Resident Engineer ────────────────────────────
        'resident_engineer_name',
        'resident_engineer_signature',
        'findings_engineer',
        'recommendation_engineer',

        // ── MTQA / Checked By ────────────────────────────────────────
        'checked_by_mtqa',//
        'mtqa_signature',//
        'recommended_action',//

        // ── Reviewed By ──────────────────────────────────────────────
        'reviewed_by',
        'reviewer_designation',
        'reviewed_by_notes',

        // ── Recommending Approval ────────────────────────────────────
        'recommending_approval_by',
        'recommending_approval_designation',
        'recommending_approval_signature',
        'recommending_approval_notes',

        // ── Approved ─────────────────────────────────────────────────
        'approved_by',
        'approved_by_designation',
        'approved_signature',
        'approved_notes',

        // ── Acceptance ───────────────────────────────────────────────
        'accepted_by_contractor',
        'accepted_date',
        'accepted_time',

        // ── Status & Notes ───────────────────────────────────────────
        'status',
        'notes',
    ];

    protected $casts = [
        'requested_work_start_date' => 'date',
        'accepted_date'             => 'date',
        'received_date'             => 'date',
        'estimated_quantity'        => 'decimal:2',
        'quantity'                  => 'decimal:2',
    ];

    protected $appends = ['submitted_by', 'submitted_date'];

    // ---------------------------------------------------------------
    // Status constants
    // ---------------------------------------------------------------
    const STATUS_DRAFT     = 'draft';
    const STATUS_SUBMITTED = 'submitted';
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
            self::STATUS_INSPECTED,
            self::STATUS_REVIEWED,
            self::STATUS_APPROVED,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
        ];
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function logs()
    {
        return $this->hasMany(WorkRequestLog::class)->orderBy('created_at', 'desc');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePendingApproval($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SUBMITTED,
            self::STATUS_INSPECTED,
            self::STATUS_REVIEWED,
        ]);
    }

    // ---------------------------------------------------------------
    // Predicates
    // ---------------------------------------------------------------

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canEdit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SUBMITTED]);
    }

    // ---------------------------------------------------------------
    // Accessors
    // ---------------------------------------------------------------

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

    // ---------------------------------------------------------------
    // Validation
    // ---------------------------------------------------------------

    public static function validationRules($id = null): array
    {
        return [
            // Project Information
            'reference_number'              => 'nullable|string|max:255|unique:work_requests,reference_number,' . $id,
            'name_of_project'               => 'required|string|max:255',
            'project_location'              => 'required|string|max:255',

            // Addressed To / From
            'for_office'                    => 'nullable|string|max:255',
            'from_requester'                => 'nullable|string|max:255',

            // Request Details
            'requested_work_start_date'     => 'required|date',
            'requested_work_start_time'     => 'nullable|string|max:20',

            // Work Details
            'item_no'                       => 'nullable|string|max:100',
            'description'                   => 'nullable|string|max:255',
            'equipment_to_be_used'          => 'nullable|string|max:255',
            'estimated_quantity'            => 'nullable|numeric|min:0',
            'quantity'                      => 'nullable|numeric|min:0',
            'unit'                          => 'nullable|string|max:50',
            'description_of_work_requested' => 'required|string',

            // Submission
            'contractor_name'               => 'nullable|string|max:255',

            // Reception
            'received_by'                   => 'nullable|string|max:255',
            'received_date'                 => 'nullable|date',
            'received_time'                 => 'nullable|string|max:20',

            // Inspection: Site Inspector
            'inspected_by_site_inspector'   => 'nullable|string|max:255',
            'site_inspector_signature'      => 'nullable|string',
            'findings_comments'             => 'nullable|string',
            'recommendation'                => 'nullable|string',

            // Inspection: Surveyor
            'surveyor_name'                 => 'nullable|string|max:255',
            'surveyor_signature'            => 'nullable|string',
            'findings_surveyor'             => 'nullable|string',
            'recommendation_surveyor'       => 'nullable|string',

            // Inspection: Resident Engineer
            'resident_engineer_name'        => 'nullable|string|max:255',
            'resident_engineer_signature'   => 'nullable|string',
            'findings_engineer'             => 'nullable|string',
            'recommendation_engineer'       => 'nullable|string',

            // MTQA / Checked By
            'checked_by_mtqa'               => 'nullable|string|max:255',
            'mtqa_signature'                => 'nullable|string',
            'recommended_action'            => 'nullable|string',

            // Reviewed By
            'reviewed_by'                   => 'nullable|string|max:255',
            'reviewer_designation'          => 'nullable|string|max:255',
            'reviewed_by_notes'             => 'nullable|string',

            // Recommending Approval
            'recommending_approval_by'          => 'nullable|string|max:255',
            'recommending_approval_designation' => 'nullable|string|max:255',
            'recommending_approval_signature'   => 'nullable|string',
            'recommending_approval_notes'       => 'nullable|string',

            // Approved
            'approved_by'                   => 'nullable|string|max:255',
            'approved_by_designation'       => 'nullable|string|max:255',
            'approved_signature'            => 'nullable|string',
            'approved_notes'                => 'nullable|string',

            // Acceptance
            'accepted_by_contractor'        => 'nullable|string|max:255',
            'accepted_date'                 => 'nullable|date',
            'accepted_time'                 => 'nullable|string|max:20',

            // Status & Notes
            'status'                        => 'nullable|string|in:' . implode(',', self::getStatuses()),
            'notes'                         => 'nullable|string',
        ];
    }

    // ---------------------------------------------------------------
    // Logging helpers
    // ---------------------------------------------------------------

    public function addLog(string $event, array $data = []): WorkRequestLog
    {
        return $this->logs()->create([
            'event'       => $event,
            'user_id'     => $data['user_id'] ?? Auth::id(), // change employee_id to user_id
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