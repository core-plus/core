<?php

namespace Core\AtMessage\Resources;

use InvalidArgumentException;
use Core\Models\User as UserModel;
use Core\Types\Models as ModelTypes;
use Core\AtMessage\ResourceInterface;
use Plus\Feed\Models\Feed as FeedModel;

class Feed implements ResourceInterface
{
    /**
     * The feed resource.
     * @var \Plus\Feed\Models\Feed
     */
    protected $feed;

    /**
     * Sender resource.
     * @var \Core\Models\User
     */
    protected $sender;

    /**
     * Create a feed resource.
     * @param \Plus\Feed\Models\Feed $feed
     * @param \Core\Models\User $sender
     */
    public function __construct(FeedModel $feed, UserModel $sender)
    {
        $this->feed = $feed;
        $this->sender = $sender;
    }

    /**
     * Get the resourceable type.
     * @return string
     */
    public function type(): string
    {
        $alise = ModelTypes::$types[FeedModel::class] ?? null;

        if (is_null($alise)) {
            throw new InvalidArgumentException('不支持的资源');
        }

        return $alise;
    }

    /**
     * Get the resourceable id.
     * @return int
     */
    public function id(): int
    {
        return $this->feed->id;
    }

    /**
     * Get the pusher message.
     * @return string
     */
    public function message(): string
    {
        return sprintf('%s在动态中@了你', $this->sender->name);
    }
}
