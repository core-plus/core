<?php

namespace Core\Models\Relations;

use Core\Models\WalletCharge;

trait UserHasWalletCharge
{
    /**
     * User wallet charges.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author GEO <dev@kaifa.me>
     */
    public function walletCharges()
    {
        return $this->hasMany(WalletCharge::class, 'user_id', 'id');
    }
}
