<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'display_name', 'description'];
}
