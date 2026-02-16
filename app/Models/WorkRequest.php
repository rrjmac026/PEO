<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class WorkRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Project Information
        'name_of_project',
        'project_location',

        // For and From (addressed to/from)
        'for_office',
        'from_requester',

        // Request Details
        'requested_by',
        'requested_work_start_date',
        'requested_work_start_time',

        // Pay Item Details
        'item_no',
        'description',
        'equipment_to_be_used',
        'estimated_quantity',
        'unit',
        'description_of_work_requested',

        // Submission
        'submitted_by',
        'submitted_date',
        'contractor_name',

        // Inspection
        'inspected_by_site_inspector',
        'site_inspector_signature',
        'surveyor_name',
        'surveyor_signature',
        'resident_engineer_name',
        'resident_engineer_signature',

        // Findings and Recommendations
        'findings_comments',
        'recommendation',
        'recommended_action',

        // Review and Approval
        'checked_by_mtqa',
        'mtqa_signature',
        'reviewed_by',
        'reviewer_designation',
        'recommending_approval_by',
        'recommending_approval_designation',
        'recommending_approval_signature',
        'approved_by',
        'approved_by_designation',
        'approved_signature',

        // Acceptance
        'accepted_by_contractor',
        'accepted_date',
        'accepted_time',
        'received_by',
        'received_date',
        'received_time',

        // Status
        'status',
        'notes',
    ];

    protected $casts = [
        'requested_work_start_date' => 'date',
        'submitted_date'            => 'date',
        'accepted_date'             => 'date',
        'received_date'             => 'date',
        'estimated_quantity'        => 'decimal:2',
    ];

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
        return $this->hasMany(WorkRequestLog::class)->latest();
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

    // ---------------------------------------------------------------
    // Validation
    // ---------------------------------------------------------------

    public static function validationRules($id = null): array
    {
        return [
            'name_of_project'              => 'required|string|max:255',
            'project_location'             => 'required|string|max:255',
            'for_office'                   => 'nullable|string|max:255',
            'from_requester'               => 'nullable|string|max:255',
            'requested_by'                 => 'required|string|max:255',
            'requested_work_start_date'    => 'required|date',
            'description_of_work_requested'=> 'required|string',
            'contractor_name'              => 'nullable|string|max:255',
            'estimated_quantity'           => 'nullable|numeric|min:0',
            'unit'                         => 'nullable|string|max:50',
            'equipment_to_be_used'         => 'nullable|string|max:255',
            'status'                       => 'required|in:' . implode(',', self::getStatuses()),
        ];
    }

    // ---------------------------------------------------------------
    // Logging helpers
    // ---------------------------------------------------------------

    /**
     * Record any event on this work request.
     *
     * @param  string       $event      WorkRequestLog::EVENT_* constant
     * @param  array        $options    [
     *     'description' => string,
     *     'note'        => string,
     *     'changes'     => array,   // [field => [old, new]]
     *     'status_from' => string,
     *     'status_to'   => string,
     *     'employee_id' => int,     // override auth user
     * ]
     */
    public function addLog(string $event, array $options = []): WorkRequestLog
    {
        $authUser = Auth::user();
        
        // Get the employee record for the authenticated user
        $employee = null;
        if ($authUser) {
            $employee = Employee::where('user_id', $authUser->id)->first();
        }

        return $this->logs()->create([
            'employee_id'  => $options['employee_id'] ?? $employee?->id,
            'event'        => $event,
            'status_from'  => $options['status_from'] ?? null,
            'status_to'    => $options['status_to']   ?? null,
            'description'  => $options['description'] ?? null,
            'changes'      => $options['changes']      ?? null,
            'note'         => $options['note']         ?? null,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
        ]);
    }

    /**
     * Convenience: diff $dirty against $original and return a changes array.
     * Pass the result to addLog as 'changes' => ...
     *
     * Example:
     *   $changes = $workRequest->buildChanges($request->validated());
     *   $workRequest->addLog(WorkRequestLog::EVENT_UPDATED, ['changes' => $changes]);
     */
    public function buildChanges(array $newData): array
    {
        $changes = [];

        foreach ($newData as $field => $newValue) {
            $oldValue = $this->getOriginal($field);

            // Cast both sides to string for a reliable comparison
            if ((string) $oldValue !== (string) $newValue) {
                $changes[$field] = [$oldValue, $newValue];
            }
        }

        return $changes;
    }
}