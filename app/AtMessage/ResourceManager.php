<?php

namespace Core\AtMessage;

use InvalidArgumentException;
use Core\Models\User as UserModel;

class ResourceManager implements ResourceManagerInterface
{
    /**
     * Resource map.
     * @var array
     */
    public static $map = [
        \Plus\Feed\Models\Feed::class => Resources\Feed::class,
        \Core\Models\Comment::class => Resources\Comment::class,
    ];

    /**
     * Get resource.
     * @param mixed $resource
     * @param \Core\Models\User $sender
     * @return \Core\AtMessage\ResourceInterface
     * @throws \InvalidArgumentException
     */
    public function resource($resource, UserModel $sender): ResourceInterface
    {
        $className = $this->getClassName($resource);
        $resourceClass = static::$map[$className] ?? null;
        if (! $resourceClass) {
            throw new InvalidArgumentException(sprintf(
                'Resource [%s] not supported.', $className
            ));
        }

        return new $resourceClass($resource, $sender);
    }

    /**
     * Get resource class name.
     * @param mixed $resource
     * @return string
     */
    public function getClassName($resource): string
    {
        return get_class($resource);
    }
}
