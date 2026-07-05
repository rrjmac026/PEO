<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkRequestRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_request_id',
        'step',
        'user_id',
        'recommendation_text',
        'signature',
        'is_signed',
    ];

    protected $casts = [
        'is_signed' => 'boolean',
    ];

    public function workRequest()
    {
        return $this->belongsTo(WorkRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Human-readable label for the step, reusing WorkRequest's step labels.
     */
    public function getStepLabelAttribute(): string
    {
        return match ($this->step) {
            'site_inspector'      => 'Site Inspector',
            'surveyor'            => 'Surveyor',
            'resident_engineer'   => 'Resident Engineer',
            'mtqa'                => 'MTQA',
            'engineer_iv'         => 'Engineer IV',
            'engineer_iii'        => 'Engineer III',
            'provincial_engineer' => 'Provincial Engineer',
            default               => ucfirst(str_replace('_', ' ', $this->step)),
        };
    }
}