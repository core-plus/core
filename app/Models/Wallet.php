<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];

    /**
     * Get the user of the wallet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author GEO <dev@kaifa.me>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
