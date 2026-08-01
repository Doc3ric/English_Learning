<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mistake extends Model
{
    protected $fillable = ['wrong_text', 'correct_text', 'reason', 'category', 'times_reviewed'];
}
