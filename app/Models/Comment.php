<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('user', function (Builder $query) {
            $query->with('user');
        });
        static::addGlobalScope('reply', function (Builder $query) {
            $query->with([
                'reply' => function (BelongsTo $belongsTo) {
                    $belongsTo->withoutGlobalScope('certification');
                },
            ]);
        });
    }

    /**
     * Has commentable.
     *
     * @return MorphTo
     * @author GEO <dev@kaifa.me>
     */
    public function commentable()
    {
        return $this->morphTo('commentable');
    }

    /**
     * Has a user.
     *
     * @return BelongsTo
     * @author GEO <dev@kaifa.me>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->withTrashed();
    }

    /**
     * 被回复者.
     *
     * @Author   Wayne
     * @DateTime 2018-04-14
     * @Email    qiaobin@dev.com
     * @return BelongsTo
     */
    public function target()
    {
        return $this->belongsTo(User::class, 'target_user', 'id');
    }

    public function blacks()
    {
        return $this->hasMany(BlackList::class, 'target_id', 'user_id');
    }

    /**
     * 被回复者.
     *
     * @Author   Wayne
     * @DateTime 2018-04-14
     * @Email    qiaobin@dev.com
     * @return BelongsTo
     */
    public function reply()
    {
        return $this->belongsTo(User::class, 'reply_user', 'id');
    }

    /**
     * 被举报记录.
     *
     * @return MorphMany
     * @author GEO <dev@kaifa.me>
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
