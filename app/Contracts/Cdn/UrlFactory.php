<?php

namespace Core\Contracts\Cdn;

use Core\Models\File;

interface UrlFactory
{
    /**
     * Get URL generator.
     *
     * @param string $name
     * @return \Core\Contracts\Cdn\UrlGenerator
     * @author GEO <dev@kaifa.me>
     */
    public function generator(string $name = ''): UrlGenerator;

    /**
     * Make a file url.
     *
     * @param \Core\Models\File $file
     * @param array $extra
     * @param string $name
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function make(File $file, array $extra = [], string $name = ''): string;
}
