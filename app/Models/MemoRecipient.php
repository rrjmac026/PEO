<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemoRecipient extends Model
{
    protected $table = 'memo_recipients';

    protected $fillable = [
        'memo_id',
        'user_id',
        'read_at',
        'email_sent_at',
        'email_failed',
    ];

    protected $casts = [
        'read_at'       => 'datetime',
        'email_sent_at' => 'datetime',
        'email_failed'  => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function markRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    public function getIsReadAttribute(): bool
    {
        return ! is_null($this->read_at);
    }
}