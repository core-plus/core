<?php

namespace Core\Models\Relations;

use Core\Models\FileWith;

trait UserHasFilesWith
{
    /**
     * user files.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author GEO <dev@kaifa.me>
     */
    public function files()
    {
        return $this->hasMany(FileWith::class, 'user_id', 'id');
    }
}
