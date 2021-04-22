<?php

namespace Core\Models\Relations;

use Core\Models\Currency;

trait UserHasCurrency
{
    public static function bootUserHasCurrency()
    {
        // 用户创建后事件
        static::created(function ($user) {
            $currency = Currency::firstOrCreate(
                ['owner_id' => $user->id],
                ['type' => 1, 'sum' => 0]
            );

            if ($currency === false) {
                return false;
            }
        });
    }

    /**
     * user has currencies.
     *
     * @author GEO <dev@kaifa.me>
     */
    public function currency()
    {
        return $this->hasOne(Currency::class, 'owner_id', 'id');
    }
}
