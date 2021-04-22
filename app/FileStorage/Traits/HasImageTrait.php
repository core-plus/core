<?php

namespace Core\FileStorage\Traits;

use Closure;

trait HasImageTrait
{
    /**
     * Custom using MIME types.
     * @return null\Closure
     */
    abstract protected function useCustomTypes(): ?Closure;

    /**
     * Get support image MIME types.
     * @return array
     */
    protected function getImageMimeTypes(): array
    {
        if (! ($handler = $this->useCustomTypes())) {
            return [
                'application/x-photoshop',
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/bmp',
                'image/tiff',
                'image/webp',
            ];
        }

        return $handler();
    }

    /**
     * Check is support image type.
     * @param string $mimeTypes
     * @return bool
     */
    protected function hasImageType(string $mimeTypes): bool
    {
        return in_array($mimeTypes, $this->getImageMimeTypes());
    }
}
