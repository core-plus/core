<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class CertificationCategory extends Model
{
    use Concerns\HasAvatar;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'name';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // protected $appends = ['icon'];

    /**
     * avatar extensions.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function getAvatarExtensions(): array
    {
        return ['png', 'jpg', 'jpeg', 'bmp'];
    }

    /**
     * Avatar prefix.
     *
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function getAvatarPrefix(): string
    {
        return 'certifications';
    }

    /**
     * Get icon url.
     *
     * @return string|null
     * @author GEO <dev@kaifa.me>
     */
    public function getIconAttribute()
    {
        return $this->avatar();
    }

    /**
     * Get avatar trait.
     *
     * @return string|int
     * @author GEO <dev@kaifa.me>
     */
    public function getAvatarKey()
    {
        return $this->getKey();
    }
}
