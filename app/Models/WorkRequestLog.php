<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkRequestLog extends Model
{
    use HasFactory;

    const EVENT_CREATED        = 'created';
    const EVENT_UPDATED        = 'updated';
    const EVENT_STATUS_CHANGED = 'status_changed';
    const EVENT_SUBMITTED      = 'submitted';
    const EVENT_INSPECTED      = 'inspected';
    const EVENT_REVIEWED       = 'reviewed';
    const EVENT_APPROVED       = 'approved';
    const EVENT_REJECTED       = 'rejected';
    const EVENT_ACCEPTED       = 'accepted';
    const EVENT_DELETED        = 'deleted';
    const EVENT_RESTORED       = 'restored';

    protected $fillable = [
        'work_request_id',
        'employee_id',
        'user_id',          // ← ADDED: this is what addLog() actually saves
        'event',
        'status_from',
        'status_to',
        'description',
        'changes',
        'note',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function workRequest()
    {
        return $this->belongsTo(WorkRequest::class);
    }

    /** Legacy relationship (employee_id) — kept for backwards compat */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /** Direct relationship to the User who performed the action */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForRequest($query, int $workRequestId)
    {
        return $query->where('work_request_id', $workRequestId);
    }

    public function scopeByEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            self::EVENT_CREATED        => 'Created',
            self::EVENT_UPDATED        => 'Updated',
            self::EVENT_STATUS_CHANGED => 'Status Changed',
            self::EVENT_SUBMITTED      => 'Submitted',
            self::EVENT_INSPECTED      => 'Inspected',
            self::EVENT_REVIEWED       => 'Reviewed',
            self::EVENT_APPROVED       => 'Approved',
            self::EVENT_REJECTED       => 'Rejected',
            self::EVENT_ACCEPTED       => 'Accepted',
            self::EVENT_DELETED        => 'Deleted',
            self::EVENT_RESTORED       => 'Restored',
            default                    => ucfirst(str_replace('_', ' ', $this->event)),
        };
    }

    public function getEventColorAttribute(): string
    {
        return match ($this->event) {
            self::EVENT_CREATED        => 'blue',
            self::EVENT_UPDATED        => 'yellow',
            self::EVENT_SUBMITTED      => 'indigo',
            self::EVENT_INSPECTED      => 'cyan',
            self::EVENT_REVIEWED       => 'purple',
            self::EVENT_APPROVED,
            self::EVENT_ACCEPTED       => 'green',
            self::EVENT_REJECTED       => 'red',
            self::EVENT_DELETED        => 'gray',
            self::EVENT_RESTORED       => 'teal',
            default                    => 'gray',
        };
    }
}