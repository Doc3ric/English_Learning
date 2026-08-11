<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationMessage extends Model
{
    protected $fillable = ['session_id', 'role', 'transcript_text', 'corrections'];

    protected function casts(): array
    {
        return [
            'corrections' => 'array',
        ];
    }

    public function session()
    {
        return $this->belongsTo(ConversationSession::class, 'session_id');
    }
}
