<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class BlackList extends Model
{
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'target_id');
    }
}
