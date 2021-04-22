<?php

namespace Core\Models\Relations;

use Core\Models\User;
use Core\Models\BlackList;
use Illuminate\Support\Facades\Cache;

trait UserHasBlackList
{
    /**
     * get blacklists of current user.
     * @Author   Wayne
     * @DateTime 2018-04-08
     * @Email    qiaobin@dev.com
     * @return   [type]              [description]
     */
    public function blacklists()
    {
        return $this->hasMany(BlackList::class, 'user_id', 'id');
    }

    /**
     * is user blacked by current_user.
     * @Author   Wayne
     * @DateTime 2018-04-18
     * @Email    qiaobin@dev.com
     * @param    [type]              $user [description]
     * @return   [type]                    [description]
     */
    public function blacked($user): bool
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        $cacheKey = sprintf('user-blacked:%s,%s', $user, $this->id);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $status = $this->blacklists()
            ->where('target_id', $user)
            ->first() !== null;
        Cache::forever($cacheKey, $status);

        return $status;
    }
}
