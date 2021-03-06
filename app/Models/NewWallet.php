<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NewWallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'total_expenses', 'total_income'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wallet';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'owner_id';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The wallet owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author GEO <dev@kaifa.me>
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }
}
