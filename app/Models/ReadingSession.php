<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingSession extends Model
{
    protected $fillable = [
        'user_id',
        'topic',
        'cefr_level',
        'estimated_read_time',
        'article_text',
        'article_title',
        'article_word_count',
        'summary_response',
        'summary_score',
        'summary_feedback',
        'missing_ideas',
        'vocabulary_suggestions',
        'quiz_data',
        'quiz_score',
        'quiz_answers',
    ];

    protected $casts = [
        'quiz_data' => 'array',
        'quiz_answers' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
