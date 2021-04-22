<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class ImGroup extends Model
{
    public $table = 'im_group';

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function face()
    {
        return $this->hasOne(FileWith::class, 'id', 'group_face');
    }
}
