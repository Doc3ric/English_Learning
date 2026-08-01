<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingAttempt extends Model
{
    protected $fillable = [
        'reading_article_id', 'score', 'total_questions', 'time_taken_seconds', 'words_per_minute'
    ];

    public function article() { return $this->belongsTo(ReadingArticle::class, 'reading_article_id'); }
}
