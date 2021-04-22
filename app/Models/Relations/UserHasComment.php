<?php

namespace Core\Models\Relations;

use Core\Models\Comment;

trait UserHasComment
{
    /**
     * Has comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author GEO <dev@kaifa.me>
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }
}
