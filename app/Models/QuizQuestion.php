<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = ['grammar_lesson_id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'explanation'];

    public function lesson()
    {
        return $this->belongsTo(GrammarLesson::class, 'grammar_lesson_id');
    }
}
