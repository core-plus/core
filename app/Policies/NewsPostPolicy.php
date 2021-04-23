<?php

namespace Core\Policies;

use Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Plus\News\Models\News;

class NewsPostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the news.
     *
     * @param  \Core\Models\User  $user
     * @param  \Plus\News\Models\News  $news
     * @return mixed
     */
    public function delete(User $user, News $news)
    {
        if ($user->id === $news->user_id) {
            return true;
        } elseif ($user->ability('[News] Delete News Post')) {
            return true;
        }

        return false;
    }
}
