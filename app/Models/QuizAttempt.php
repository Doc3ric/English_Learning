<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['grammar_lesson_id', 'score', 'passed', 'taken_at'];
}
