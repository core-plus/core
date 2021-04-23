<?php

namespace Core\AtMessage;

use Core\Models\User as UserModel;
use Core\Notifications\At as AtNotification;

class Message implements MessageInterface
{
    /**
     * Resources manager.
     * @var \Core\AtMessage\ResourceManagerInterface
     */
    protected $manager;

    /**
     * Create the message instance.
     * @param \Core\AtMessage\ResourceManagerInterface $manager
     */
    public function __construct(ResourceManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * The message send handler.
     * @param \Core\Models\User $sender
     * @param \Core\Models\User $user
     * @param mixed $resource
     * @return void
     */
    public function send(UserModel $sender, UserModel $user, $resource): void
    {
        $resource = $this->manager->resource($resource, $sender);
        $user->notify(new AtNotification($resource, $sender));
    }
}
