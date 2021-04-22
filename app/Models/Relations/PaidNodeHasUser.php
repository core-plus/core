<?php

namespace Core\Models\Relations;

use Core\Models\User;
use Core\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Plus\Feed\CacheName\CacheKeys;

trait PaidNodeHasUser
{
    // 发起支付节点人钱包.
    public function wallet()
    {
        return $this->hasManyThrough(Wallet::class, User::class, 'id',
            'user_id', 'user_id');
    }

    /**
     * Paid node users.
     *
     * @return BelongsToMany
     * @author GEO <dev@kaifa.me>
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'paid_node_users', 'node_id',
            'user_id');
    }

    /**
     * the author of paid.
     *
     * @return hasOne
     * @author GEO <dev@kaifa.me>
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * To determine whether to pay for the node, to support the filter publisher.
     *
     * @param  int  $user  User ID
     * @param  bool  $filter
     *
     * @return bool
     * @author GEO <dev@kaifa.me>
     */
    public function paid(int $user, bool $filter = true)
    : bool
    {
        if ($filter === true && $this->user_id === $user) {
            return true;
        }

        $status = Cache::rememberForever(sprintf(CacheKeys::PAID, $this->id,
            $user), function () use ($user) {
                return $this->users()->newPivotStatementForId($user)->exists();
            });

        return $status;
    }
}
