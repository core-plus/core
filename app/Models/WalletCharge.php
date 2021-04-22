<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletCharge extends Model
{
    use SoftDeletes;

    /**
     * User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author GEO <dev@kaifa.me>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
