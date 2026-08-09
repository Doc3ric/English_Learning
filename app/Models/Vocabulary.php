<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    protected $fillable = [
        'word',
        'meaning',
        'pronunciation',
        'part_of_speech',
        'example_sentence',
        'synonyms',
        'antonyms',
        'personal_note',
        'is_favorite',
        'is_mastered',
        'source',
        'source_reading_article_id',
        'leitner_box',
        'next_review_date',
        'last_reviewed_at'
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'is_mastered' => 'boolean',
        'next_review_date' => 'date',
        'last_reviewed_at' => 'datetime',
    ];

    public function readingArticle()
    {
        return $this->belongsTo(ReadingArticle::class, 'source_reading_article_id');
    }
}
