<?php

namespace Core\Models\Concerns;

use Core\Models\Role;
use Core\Services\UserAbility;

trait UserHasAbility
{
    /**
     * Abiliry service instance.
     *
     * @var \Core\Services\UserAbility
     */
    protected $ability;

    /**
     * User ability.
     *
     * @param array $parameters
     *        ability();
     *        ability($ability);
     *        ability($role, $ability);
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function ability(...$parameters)
    {
        if (isset($parameters[1])) {
            return ($role = $this->resolveAbility()->roels($parameters[0]))
                ? $role->ability($parameters[1])
                : false;
        } elseif (isset($parameters[0])) {
            return $this->resolveAbility()
                ->all($parameters[0]);
        }

        return $this->resolveAbility();
    }

    /**
     * The user all roles.
     *
     * @param string $role
     * @return mied
     * @author GEO <dev@kaifa.me>
     */
    public function roles(string $role = '')
    {
        if ($role) {
            return $this->ability()->roles($role);
        }

        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Resolve ability service.
     *
     * @return \Core\Services\UserAbility
     * @author GEO <dev@kaifa.me>
     */
    protected function resolveAbility()
    {
        if (! ($this->ability instanceof UserAbility)) {
            $this->ability = new UserAbility();
        }

        return $this->ability->setUser($this);
    }
}
