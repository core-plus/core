<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class GoldRule extends Model
{
    public $table = 'gold_rules';

    public $fillable = ['name', 'alias', 'desc', 'incremental'];
}
