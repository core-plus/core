<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FileWith extends Model
{
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = ['file_id', 'user_id', 'channel', 'raw', 'size'];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('paidNode', function (Builder $query) {
            $query->with('paidNode');
        });
    }

    public function getPayIndexAttribute(): string
    {
        return sprintf('file:%d', $this->id);
    }

    /**
     * has file.
     *
     * @return HasOne
     * @author GEO <dev@kaifa.me>
     */
    public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    /**
     * 获取付费节点.
     *
     * @return HasOne
     * @author GEO <dev@kaifa.me>
     */
    public function paidNode()
    {
        return $this->hasOne(PaidNode::class, 'raw', 'id')
            ->where('channel', 'file');
    }
}
