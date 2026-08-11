<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyPlanItem extends Model
{
    protected $fillable = [
        'daily_plan_id',
        'activity_type',
        'activity_id',
        'title',
        'description',
        'reason',
        'route_name',
        'route_params',
        'priority',
        'estimated_minutes',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'route_params' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(DailyPlan::class, 'daily_plan_id');
    }
}
