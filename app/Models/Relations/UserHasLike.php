<?php

namespace Core\Models\Relations;

use Core\Models\Like;

trait UserHasLike
{
    /**
     * Has likes for user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author GEO <dev@kaifa.me>
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id', 'id');
    }

    /**
     * Has be likeds for user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author GEO <dev@kaifa.me>
     */
    public function belikeds()
    {
        return $this->hasMany(Like::class, 'target_user', 'id');
    }
}
