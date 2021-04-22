<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Area extends Model
{
    /**
     * Scope func by pid.
     *
     * @param Builder $query
     * @param int     $pid
     *
     * @return Illuminate\Database\Eloquent\Builder
     *
     * @author GEO <dev@kaifa.me>
     * @homepage http://gitx.cn
     */
    public function scopeByPid(Builder $query, int $pid): Builder
    {
        return $query->where('pid', $pid);
    }

    /**
     * 父地区.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     *
     * @author GEO <dev@kaifa.me>
     * @homepage http://gitx.cn
     */
    public function parent()
    {
        return $this->hasOne(__CLASS__, 'id', 'pid');
    }

    /**
     * 子地区.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @author GEO <dev@kaifa.me>
     * @homepage http://gitx.cn
     */
    public function items()
    {
        return $this->hasMany(__CLASS__, 'pid');
    }
}
