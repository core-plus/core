<?php

namespace Core\Contracts\Cdn;

use Core\Cdn\Refresh;

interface UrlGenerator
{
    /**
     * Generator an absolute URL to the given path.
     *
     * @param string $filename
     * @param array $extra "[float $width, float $height, int $quality]"
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function url(string $filename, array $extra = []): string;

    /**
     * Refresh the cdn files and dirs.
     *
     * @param \Core\Cdn\Refresh $refresh
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    public function refresh(Refresh $refresh);
}
