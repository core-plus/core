<?php

namespace Core\Contracts\Model;

interface FetchComment
{
    /**
     * Get comment centent.
     *
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function getCommentContentAttribute(): string;

    /**
     * Get target source display title.
     *
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function getTargetTitleAttribute(): string;

    /**
     * Get target source image file with ID.
     *
     * @return int
     * @author GEO <dev@kaifa.me>
     */
    public function getTargetImageAttribute(): int;

    /**
     * Get target source id.
     *
     * @return int
     * @author GEO <dev@kaifa.me>
     */
    public function getTargetIdAttribute(): int;
}
