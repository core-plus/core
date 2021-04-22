<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Like extends Pivot
{
    /**
     * The model table name.
     */
    protected $table = 'likes';

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Has likeable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     * @author GEO <dev@kaifa.me>
     */
    public function likeable()
    {
        return $this->morphTo();
    }

    /**
     * Has user of the likeable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author GEO <dev@kaifa.me>
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
