<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'backup_name',
        'file_path',
        'backup_type',
        'file_size',
        'status',
        'created_by',
        'backup_date',
        'retention_until',
        'completed_at',
        'error_message',
        'progress'
    ];

    protected $casts = [
        'backup_date' => 'datetime',
        'retention_until' => 'datetime',
        'completed_at' => 'datetime',
        'file_size' => 'integer',
        'progress' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canDownload(): bool
    {
        return $this->status === 'completed' && $this->file_path;
    }

    public function canDelete(): bool
    {
        return in_array($this->status, ['completed', 'failed']);
    }

    public function isExpired(): bool
    {
        return $this->retention_until && $this->retention_until->isPast();
    }

    public function getFormattedSizeAttribute()
    {
        if (!$this->file_size) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->file_size;
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->completed_at) return null;
        
        return $this->backup_date->diffForHumans($this->completed_at, true);
    }
}