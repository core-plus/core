<?php

namespace Core\AtMessage;

use Core\Models\User as UserModel;

interface MessageInterface
{
    /**
     * Send at message.
     * @param \Core\Models\User $sender
     * @param \Core\Models\User $user
     * @param mixed $resource
     * @return void
     */
    public function send(UserModel $sender, UserModel $user, $resource): void;
}
