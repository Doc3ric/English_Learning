<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudySession extends Model
{
    protected $fillable = [
        'duration_seconds',
        'activity_type',
        'notes',
    ];
}
