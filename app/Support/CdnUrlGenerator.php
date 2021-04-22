<?php

namespace Core\Support;

use Core\Models\File;
use Core\Contracts\Cdn\UrlGenerator as FileUrlGeneratorContract;

abstract class CdnUrlGenerator implements FileUrlGeneratorContract
{
    /**
     * File data model.
     *
     * @var \Core\Models\File
     */
    protected $file;

    /**
     * Get file data model.
     *
     * @return \Core\Models\File
     * @author GEO <dev@kaifa.me>
     */
    protected function getFile(): File
    {
        return $this->file;
    }

    /**
     * Set file data model.
     *
     * @param \Core\Models\File $file
     * @return CdnUrlGenerator
     * @author GEO <dev@kaifa.me>
     */
    protected function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }
}
