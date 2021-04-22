<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class TagCategory extends Model
{
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Has tags of the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author GEO <dev@kaifa.me>
     */
    public function tags()
    {
        return $this->hasMany(Tag::class, 'tag_category_id', 'id')
            ->orderBy('weight', 'desc');
    }
}
