<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'is_read',
        'notifiable_id',
        'notifiable_type',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Send a notification to one or more users.
     */
    public static function send(
        int|array $userIds,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?Model $notifiable = null
    ): void {
        $userIds = (array) $userIds;

        $rows = array_map(function ($userId) use ($type, $title, $message, $link, $notifiable) {
            return [
                'user_id'         => $userId,
                'type'            => $type,
                'title'           => $title,
                'message'         => $message,
                'link'            => $link,
                'is_read'         => false,
                'notifiable_id'   => $notifiable?->id,
                'notifiable_type' => $notifiable ? get_class($notifiable) : null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }, $userIds);

        static::insert($rows);
    }
}