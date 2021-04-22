<?php

namespace Core\Models\Relations;

use Core\Models\User;
use Core\Models\Reward;

trait UserHasReward
{
    /**
     * 用户的被打赏记录.
     *
     * @author GEO <dev@kaifa.me>
     * @return \Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function beRewardeds()
    {
        return $this->morphMany(Reward::class, 'rewardable');
    }

    /**
     * 打赏用户.
     *
     * @author GEO <dev@kaifa.me>
     * @param  mix $user
     * @param  float $amount
     * @return mix
     */
    public function reward($user, $amount)
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        return $this->getConnection()->transaction(function () use ($user, $amount) {
            return $this->beRewardeds()->create([
                'user_id' => $user,
                'target_user' => $this->id,
                'amount' => $amount,
            ]);
        });
    }
}
