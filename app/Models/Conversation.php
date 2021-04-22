<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['type', 'user_id', 'content', 'options', 'system_mark'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function target()
    {
        return $this->hasOne(User::class, 'id', 'to_user_id');
    }
}
