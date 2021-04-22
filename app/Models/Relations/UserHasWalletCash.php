<?php

namespace Core\Models\Relations;

use Core\Models\WalletCash;

trait UserHasWalletCash
{
    /**
     * Wallet cshs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author GEO <dev@kaifa.me>
     */
    public function walletCashes()
    {
        return $this->hasMany(WalletCash::class, 'user_id', 'id');
    }
}
