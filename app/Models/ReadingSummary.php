<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingSummary extends Model
{
    protected $fillable = [
        'reading_article_id', 'summary_text', 'word_count'
    ];

    public function article() { return $this->belongsTo(ReadingArticle::class, 'reading_article_id'); }
}
