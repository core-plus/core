<?php

namespace Core\FileStorage\Traits;

use Exception;
use Core\FileStorage\Resource;
use Core\FileStorage\StorageInterface;
use Core\FileStorage\FileMetaInterface;

trait EloquentAttributeTrait
{
    /**
     * Get file storage instance.
     * @return \Core\FileStorage\StorageInterface
     */
    protected function getFileStorageInstance(): StorageInterface
    {
        return app(StorageInterface::class);
    }

    /**
     * Get resource meta.
     * @param string $resource
     * @return null|\Core\FileStorage\FileMeatInterface
     */
    protected function getFileStorageResourceMeta(string $resource): ?FileMetaInterface
    {
        try {
            return $this->getFileStorageInstance()->meta(new Resource($resource));
        } catch (Exception $e) {
            return null;
        }

        return $resource;
    }
}
