<?php

namespace Core\Models\Relations;

use Core\Models\NewWallet;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait UserHasNewWallet
{
    /**
     * Bootstrap the trait.
     *
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    public static function bootUserHasNewWallet()
    {
        // 用户创建后事件
        static::created(function ($user) {
            $wallet = new NewWallet();
            $wallet->owner_id = $user->id;
            $wallet->balance = 0;
            $wallet->total_income = 0;
            $wallet->total_expenses = 0;
            $wallet->save();

            if ($wallet === false) {
                return false;
            }
        });

        // 用户删除后事件
        // static::deleted(function ($user) {
        //     $user->wallet()->delete();
        // });
    }

    /**
     * User wallet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author GEO <dev@kaifa.me>
     */
    public function newWallet(): HasOne
    {
        return $this->hasOne(NewWallet::class, 'owner_id', 'id');
    }
}
