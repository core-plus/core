<?php

namespace Core\Models;

use Core\CacheNames;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class GoldType extends Model
{
    public $table = 'gold_types';

    public $fillable = ['name', 'unit', 'status'];

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
            return self::query()->where('status', 1)
               ->select('name', 'unit')
               ->first() ?? collect(['name' => '金币', 'unit' => '个']);
        });

        return $field ? $gold->$field : $gold;
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
