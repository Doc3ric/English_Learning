<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyPlan extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'target_minutes',
        'completed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function items()
    {
        return $this->hasMany(DailyPlanItem::class, 'daily_plan_id')->orderBy('priority', 'desc');
    }
}
