<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingArticle extends Model
{
    protected $fillable = [
        'title', 'level', 'target_band', 'full_text', 'source_url', 'recommended_time_minutes'
    ];

    public function questions() { return $this->hasMany(ReadingQuestion::class); }
    public function attempts() { return $this->hasMany(ReadingAttempt::class); }
    public function summaries() { return $this->hasMany(ReadingSummary::class); }
    public function vocabularies() { return $this->hasMany(Vocabulary::class, 'source_reading_article_id'); }
}
