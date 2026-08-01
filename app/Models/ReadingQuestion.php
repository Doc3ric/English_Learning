<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingQuestion extends Model
{
    protected $fillable = [
        'reading_article_id', 'question_text', 'question_type',
        'option_a', 'option_b', 'option_c', 'option_d',
        'correct_answer', 'explanation'
    ];

    public function article() { return $this->belongsTo(ReadingArticle::class, 'reading_article_id'); }
}
