<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReflection extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'did_grammar',
        'did_vocabulary',
        'did_speaking',
        'did_writing',
        'what_was_difficult',
        'new_expression',
    ];

    protected function casts(): array
    {
        return [
            'did_grammar'    => 'boolean',
            'did_vocabulary' => 'boolean',
            'did_speaking'   => 'boolean',
            'did_writing'    => 'boolean',
            'date'           => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
