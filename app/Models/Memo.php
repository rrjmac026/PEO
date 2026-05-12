<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Memo extends Model
{
    use HasFactory, SoftDeletes;

    // ── Type constants ────────────────────────────────────────────────────────
    const TYPE_ANNOUNCEMENT      = 'announcement';
    const TYPE_BIRTHDAY          = 'birthday';
    const TYPE_HOLIDAY_GREETING  = 'holiday_greeting';
    const TYPE_POLICY_UPDATE     = 'policy_update';
    const TYPE_EVENT_INVITATION  = 'event_invitation';
    const TYPE_PERFORMANCE_NOTICE = 'performance_notice';
    const TYPE_GENERAL           = 'general';

    // ── Status constants ──────────────────────────────────────────────────────
    const STATUS_DRAFT     = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENT      = 'sent';
    const STATUS_CANCELLED = 'cancelled';

    // ── Scope constants ───────────────────────────────────────────────────────
    const SCOPE_ALL        = 'all';
    const SCOPE_BY_ROLE    = 'by_role';
    const SCOPE_BY_DEPT    = 'by_department';
    const SCOPE_SPECIFIC   = 'specific';

    protected $fillable = [
        'reference_number',
        'type',
        'subject',
        'body',
        'sent_by_user_id',
        'status',
        'scheduled_at',
        'sent_at',
        'recipient_scope',
        'target_roles',
        'target_departments',
        'attachments',
    ];

    protected $casts = [
        'scheduled_at'       => 'datetime',
        'sent_at'            => 'datetime',
        'target_roles'       => 'array',
        'target_departments' => 'array',
        'attachments'        => 'array',
    ];

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->reference_number)) {
                $year  = now()->format('Y');
                $count = static::whereYear('created_at', $year)->withTrashed()->count() + 1;
                $model->reference_number = sprintf('MEMO-%s-%04d', $year, $count);
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }

    public function memoRecipients(): HasMany
    {
        return $this->hasMany(MemoRecipient::class);
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memo_recipients')
                    ->withPivot(['read_at', 'email_sent_at', 'email_failed'])
                    ->withTimestamps();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeDraft($query)       { return $query->where('status', self::STATUS_DRAFT); }
    public function scopeSent($query)        { return $query->where('status', self::STATUS_SENT); }
    public function scopeScheduled($query)   { return $query->where('status', self::STATUS_SCHEDULED); }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('subject',          'LIKE', "%{$term}%")
              ->orWhere('body',            'LIKE', "%{$term}%")
              ->orWhere('reference_number','LIKE', "%{$term}%")
              ->orWhereHas('sender', fn ($q2) => $q2->where('name', 'LIKE', "%{$term}%"));
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function types(): array
    {
        return [
            self::TYPE_ANNOUNCEMENT       => 'Announcement',
            self::TYPE_BIRTHDAY           => 'Birthday Greeting',
            self::TYPE_HOLIDAY_GREETING   => 'Holiday Greeting',
            self::TYPE_POLICY_UPDATE      => 'Policy Update',
            self::TYPE_EVENT_INVITATION   => 'Event Invitation',
            self::TYPE_PERFORMANCE_NOTICE => 'Performance Notice',
            self::TYPE_GENERAL            => 'General Memo',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->type] ?? ucfirst($this->type);
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ANNOUNCEMENT       => 'fa-bullhorn',
            self::TYPE_BIRTHDAY           => 'fa-birthday-cake',
            self::TYPE_HOLIDAY_GREETING   => 'fa-gift',
            self::TYPE_POLICY_UPDATE      => 'fa-file-shield',
            self::TYPE_EVENT_INVITATION   => 'fa-calendar-star',
            self::TYPE_PERFORMANCE_NOTICE => 'fa-chart-line',
            default                       => 'fa-envelope',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ANNOUNCEMENT       => 'blue',
            self::TYPE_BIRTHDAY           => 'pink',
            self::TYPE_HOLIDAY_GREETING   => 'green',
            self::TYPE_POLICY_UPDATE      => 'orange',
            self::TYPE_EVENT_INVITATION   => 'purple',
            self::TYPE_PERFORMANCE_NOTICE => 'yellow',
            default                       => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SENT      => 'green',
            self::STATUS_SCHEDULED => 'blue',
            self::STATUS_DRAFT     => 'gray',
            self::STATUS_CANCELLED => 'red',
            default                => 'gray',
        };
    }

    public function getReadCountAttribute(): int
    {
        return $this->memoRecipients()->whereNotNull('read_at')->count();
    }

    public function getRecipientCountAttribute(): int
    {
        return $this->memoRecipients()->count();
    }

    public function getReadRateAttribute(): float
    {
        $total = $this->recipient_count;
        return $total > 0 ? round(($this->read_count / $total) * 100) : 0;
    }

    /**
     * Resolve the concrete list of User IDs for this memo's scope.
     */
    public function resolveRecipientUserIds(): array
    {
        $query = User::query();

        return match ($this->recipient_scope) {
            self::SCOPE_ALL => $query->pluck('id')->toArray(),

            self::SCOPE_BY_ROLE => $query
                ->whereIn('role', $this->target_roles ?? [])
                ->pluck('id')->toArray(),

            self::SCOPE_BY_DEPT => $query
                ->whereHas('employee', fn ($q) =>
                    $q->whereIn('department', $this->target_departments ?? [])
                )
                ->pluck('id')->toArray(),

            // SCOPE_SPECIFIC: already stored in memo_recipients — just return them
            default => $this->memoRecipients()->pluck('user_id')->toArray(),
        };
    }
}