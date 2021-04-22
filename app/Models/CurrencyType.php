<?php

namespace Core\Models;

use Core\CacheNames;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CurrencyType extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 获取当前使用的积分名称.
     *
     * @param  null  $field
     *
     * @return mixed
     */
    public static function current($field = null)
    {
        $gold = Cache::rememberForever(CacheNames::CURRENCY_NAME, function () {
            $current = self::query()->where('enable', 1)
                ->select('id', 'name', 'unit')
                ->first();

            return collect($current ?
                $current->toArray() :
                ['id' => 999, 'name' => '金币', 'unit'=> '个']);
        });

        return $field ? $gold->get($field) : $gold;
    }

    /**
     * 设置当前使用的积分名称.
     *
     * @param  string  $name
     * @param  string  $unit
     */
    public static function setCurrent(string $name, string $unit)
    {
        Cache::forever(CacheNames::CURRENCY_NAME, collect([
            'name' => $name,
            'unit' => $unit,
        ]));
    }
}
