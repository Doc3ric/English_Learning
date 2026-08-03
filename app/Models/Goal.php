<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'target_vocabulary',
        'target_grammar',
        'target_reading',
        'target_writing',
        'target_study_time',
    ];
}
