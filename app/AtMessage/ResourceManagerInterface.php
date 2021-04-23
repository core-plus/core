<?php

namespace Core\AtMessage;

use Core\Models\User as UserModel;

interface ResourceManagerInterface
{
    /**
     * Get resource.
     * @param mixed $resource
     * @param \Core\Models\User $sender
     * @return \Core\AtMessage\ResourceInterface
     */
    public function resource($resource, UserModel $sender): ResourceInterface;

    /**
     * Get resource class name.
     * @param mixed $resource
     * @return string
     */
    public function getClassName($resource): string;
}
