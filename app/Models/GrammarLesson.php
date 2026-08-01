<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrammarLesson extends Model
{
    protected $fillable = ['title', 'content', 'order_index', 'is_completed', 'is_generated'];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
