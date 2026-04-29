<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConcretePouringLog extends Model
{
    // ── Event constants ───────────────────────────────────────────────────────
    const EVENT_SUBMITTED      = 'submitted';       // Contractor submitted request
    const EVENT_UPDATED        = 'updated';         // Contractor edited request
    const EVENT_DELETED        = 'deleted';         // Contractor deleted request
    const EVENT_ASSIGNED       = 'assigned';        // Admin assigned reviewers
    const EVENT_RE_REVIEWED    = 're_reviewed';     // Resident Engineer submitted review
    const EVENT_PE_NOTED       = 'pe_noted';        // Provincial Engineer submitted note
    const EVENT_MTQA_DECIDED   = 'mtqa_decided';    // MTQA made final decision
    const EVENT_APPROVED       = 'approved';        // Final status: approved
    const EVENT_DISAPPROVED    = 'disapproved';     // Final status: disapproved
    const EVENT_STATUS_CHANGED = 'status_changed';  // Any other status transition

    protected $table = 'concrete_pouring_logs';

    protected $fillable = [
        'concrete_pouring_id',
        'user_id',
        'event',
        'description',
        'note',
        'changes',
        'review_step',
        'status_from',
        'status_to',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function concretePouring(): BelongsTo
    {
        return $this->belongsTo(ConcretePouring::class, 'concrete_pouring_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Human-readable label for the event key.
     */
    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            self::EVENT_SUBMITTED      => 'Request Submitted',
            self::EVENT_UPDATED        => 'Request Updated',
            self::EVENT_DELETED        => 'Request Deleted',
            self::EVENT_ASSIGNED       => 'Reviewers Assigned',
            self::EVENT_RE_REVIEWED    => 'Resident Engineer Review',
            self::EVENT_PE_NOTED       => 'Provincial Engineer Note',
            self::EVENT_MTQA_DECIDED   => 'MTQA Final Decision',
            self::EVENT_APPROVED       => 'Approved',
            self::EVENT_DISAPPROVED    => 'Disapproved',
            self::EVENT_STATUS_CHANGED => 'Status Changed',
            default                    => ucfirst(str_replace('_', ' ', $this->event)),
        };
    }

    /**
     * Tailwind / Bootstrap color class for the event badge.
     */
    public function getEventColorAttribute(): string
    {
        return match ($this->event) {
            self::EVENT_SUBMITTED      => 'blue',
            self::EVENT_UPDATED        => 'yellow',
            self::EVENT_DELETED        => 'red',
            self::EVENT_ASSIGNED       => 'indigo',
            self::EVENT_RE_REVIEWED    => 'cyan',
            self::EVENT_PE_NOTED       => 'purple',
            self::EVENT_MTQA_DECIDED   => 'orange',
            self::EVENT_APPROVED       => 'green',
            self::EVENT_DISAPPROVED    => 'red',
            self::EVENT_STATUS_CHANGED => 'gray',
            default                    => 'gray',
        };
    }

    /**
     * Icon class (Heroicons / Font Awesome) to pair with each event.
     */
    public function getEventIconAttribute(): string
    {
        return match ($this->event) {
            self::EVENT_SUBMITTED      => 'fa-paper-plane',
            self::EVENT_UPDATED        => 'fa-pen',
            self::EVENT_DELETED        => 'fa-trash',
            self::EVENT_ASSIGNED       => 'fa-user-check',
            self::EVENT_RE_REVIEWED    => 'fa-hard-hat',
            self::EVENT_PE_NOTED       => 'fa-stamp',
            self::EVENT_MTQA_DECIDED   => 'fa-gavel',
            self::EVENT_APPROVED       => 'fa-circle-check',
            self::EVENT_DISAPPROVED    => 'fa-circle-xmark',
            self::EVENT_STATUS_CHANGED => 'fa-arrows-rotate',
            default                    => 'fa-clock',
        };
    }

    /**
     * Actor label — falls back to role-based label if user is deleted.
     */
    public function getActorNameAttribute(): string
    {
        return $this->user?->name ?? 'System';
    }

    public function getActorRoleAttribute(): string
    {
        return match ($this->user?->role) {
            'admin'               => 'Admin',
            'contractor'          => 'Contractor',
            'resident_engineer'   => 'Resident Engineer',
            'provincial_engineer' => 'Provincial Engineer',
            'mtqa'                => 'ME/MTQA',
            default               => 'User',
        };
    }
}
