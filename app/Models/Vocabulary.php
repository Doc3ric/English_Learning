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
        'source_reading_article_id'
    ];

    public function readingArticle()
    {
        return $this->belongsTo(ReadingArticle::class, 'source_reading_article_id');
    }
}
