<?php

namespace Core\Policies\Feed;

use Core\Models\User as UserModel;
use Core\Models\FeedTopic as FeedTopicModel;

class Topic
{
    /**
     * Check the topic can be operated by the user.
     *
     * @param \Core\Models\User $user
     * @param \Core\Models\FeedTopic $topic
     * @return bool
     */
    public function update(UserModel $user, FeedTopicModel $topic): bool
    {
        if ($user->ability('admin: update feed topic')) {
            return true;
        }

        return $user->id === $topic->creator_user_id;
    }
}
