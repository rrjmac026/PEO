<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'position',
        'department',
        'email',
        'phone',
        'office',
        'signature_path',
    ];

    /**
     * Get all work requests submitted by this employee
     */
    public function workRequests()
    {
        return $this->hasMany(WorkRequest::class);
    }

    /**
     * Search employees by name, ID, or department
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('employee_id', 'LIKE', "%{$term}%")
            ->orWhere('department', 'LIKE', "%{$term}%")
            ->orWhere('position', 'LIKE', "%{$term}%")
            ->orWhereHas('user', function ($q2) use ($term) {
                $q2->where('name', 'LIKE', "%{$term}%");
            });
        });
    }

    /**
     * Get formatted name with position
     */
    public function getNameWithPositionAttribute()
    {
        return "{$this->user?->name} - {$this->position}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}