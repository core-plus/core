<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyOrder extends Model
{
    /**
     * the owner of order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author GEO <dev@kaifa.me>
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }
}
