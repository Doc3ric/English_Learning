<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WritingSession extends Model
{
    protected $fillable = [
        'user_id',
        'prompt_topic',
        'user_response',
        'word_count',
        'ai_corrected_version',
        'ai_explanation',
        'grammar_score',
        'vocabulary_score',
        'naturalness_score',
        'clarity_score',
        'cefr_estimate',
        'rewrite_attempt',
        'professional_version',
        'native_version',
        'memory_context',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
