<?php

namespace Core\Models\Relations;

use Core\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait UserHasFollow
{
    /**
     * follows - my following.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author GEO <dev@kaifa.me>
     */
    public function followings(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'user_follow', 'user_id', 'target')
            ->withPivot('id')
            ->orderBy('user_follow.id', 'desc')
            ->withTimestamps();
    }

    /**
     * followers - my followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author GEO <dev@kaifa.me>
     */
    public function followers(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'user_follow', 'target', 'user_id')
            ->withPivot('id')
            ->orderBy('user_follow.id', 'desc')
            ->withTimestamps();
    }

    /**
     * Verification is concerned followed.
     *
     * @param int|\Core\Models\User $user
     * @return bool
     * @author GEO <dev@kaifa.me>
     */
    public function hasFollwing($user): bool
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        if (! $user) {
            return false;
        }

        return $this
            ->followings()
            ->newPivotStatementForId($user)
            ->value('target') === $user;
    }

    /**
     * Verify that I am followed.
     *
     * @param  int|\Core\Models\User $user
     * @return bool
     * @author GEO <dev@kaifa.me>
     */
    public function hasFollower($user): bool
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        if (! $user) {
            return false;
        }

        return $this
            ->followers()
            ->newPivotStatementForId($user)
            ->value('user_id') === $user;
    }

    /**
     * 相互关注的好友.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * @author ZsyD<1251992018@qq.com>
     */
    public function mutual(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'user_follow', 'user_id', 'target')
            ->join('user_follow as b', function ($join) {
                $join->on('user_follow.user_id', '=', 'b.target')
                ->on('user_follow.target', '=', 'b.user_id');
            })
            ->withPivot('id')
            ->orderBy('user_follow.id', 'desc')
            ->withTimestamps();
    }
}
