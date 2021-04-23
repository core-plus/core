<?php

namespace Core\Contracts\Model;

interface UserAbility
{
    /**
     * get users all roles.
     *
     * @param string $role
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function roles(string $role = '');

    /**
     * Get users all abilities.
     *
     * @param string $ability
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function all(string $ability = '');
}
