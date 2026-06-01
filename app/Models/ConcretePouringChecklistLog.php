<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcretePouringChecklistLog extends Model
{
    protected $table = 'concrete_pouring_checklist_logs';

    protected $fillable = [
        'concrete_pouring_id',
        'user_id',
        'field',
        'checked',
    ];

    protected $casts = [
        'checked' => 'boolean',
    ];

    public function concretePouring()
    {
        return $this->belongsTo(ConcretePouring::class, 'concrete_pouring_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}