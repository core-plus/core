<?php

namespace Core\Models\Relations;

use Core\Models\RoleUser;

trait UserHasRole
{
    /**
     * Has roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function administrator()
    {
        return $this->HasMany(RoleUser::class, 'user_id', 'id')
            ->where('role_id', 1);
    }
}
