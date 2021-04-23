<?php

namespace Core\AtMessage\Resources;

use Core\Models\User as UserModel;
use Core\Types\Models as ModelTypes;
use Core\AtMessage\ResourceInterface;
use Core\Models\Comment as CommentModel;

class Comment implements ResourceInterface
{
    /**
     * The comment resource.
     * @var \Core\Models\Comment
     */
    protected $comment;

    /**
     * The sender.
     * @var \Core\Models\User
     */
    protected $sender;

    /**
     * Create the resource.
     * @param \Core\Models\Comment $comment
     * @param \Core\Models\User $sender
     */
    public function __construct(CommentModel $comment, UserModel $sender)
    {
        $this->comment = $comment;
        $this->sender = $sender;
    }

    /**
     * Get the resourceable type.
     * @return string
     */
    public function type(): string
    {
        $alise = ModelTypes::$types[CommentModel::class] ?? null;

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
        return $this->comment->id;
    }

    /**
     * Get the resourceable push message.
     * @return string
     */
    public function message(): string
    {
        return sprintf('%s在评论中@了你', $this->sender->name);
    }
}
