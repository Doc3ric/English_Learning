<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationSession extends Model
{
    protected $fillable = ['user_id', 'scenario'];

    public function messages()
    {
        return $this->hasMany(ConversationMessage::class, 'session_id');
    }
}
