<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

        // Personal
        'first_name',
        'last_name',
        'middle_name',
        'email_address',
        'date_of_birth',
        'blood_type',
        'height_cm',
        'weight_kg',
        'home_address',
        'phone_number',
        'emergency_contact_no',

        // Government IDs
        'id_number',
        'tin',
        'pagibig_no',
        'philhealth',
        'gsis_no',

        // HMO
        'hmo_organization',
        'hmo_number',

        // Professional
        'eligibility',
        'position_title',
        'licence_number',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'height_cm'     => 'decimal:2',
        'weight_kg'     => 'decimal:2',
    ];

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            trim($this->first_name  ?? ''),
            trim($this->middle_name ?? ''),
            trim($this->last_name   ?? ''),
        ]);

        return implode(' ', $parts) ?: ($this->user?->name ?? '');
    }

    public function getNameWithPositionAttribute(): string
    {
        return "{$this->full_name} - {$this->position_title}";
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%' . $term . '%';

        return $query->where(function ($q) use ($like) {
            $q->where('id_number',      'LIKE', $like)
            ->orWhere('first_name',   'LIKE', $like)
            ->orWhere('last_name',    'LIKE', $like)
            ->orWhere('position_title', 'LIKE', $like)
            ->orWhere('department',   'LIKE', $like)
            ->orWhereHas('user', fn ($u) => $u->where('name', 'LIKE', $like));
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}