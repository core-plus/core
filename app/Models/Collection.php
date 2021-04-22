<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Has collectible.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     * @author GEO <dev@kaifa.me>
     */
    public function collectible()
    {
        return $this->morphTo();
    }
}
