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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
