<?php

namespace Core\FileStorage\Validators\Rulers;

use Exception;
use Core\FileStorage\Resource;
use Core\FileStorage\StorageInterface;

class FileStorageRuler implements RulerInterface
{
    /**
     * The storage.
     *
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Create the ruler instance.
     *
     * @param  StorageInterface  $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Rule handler.
     *
     * @param  array  $params
     *
     * @return bool
     */
    public function handle(array $params)
    : bool
    {
        try {
            return (bool) $this->storage
                ->meta(new Resource($params[1]))
                ->getSize();
        } catch (Exception $e) {
            return false;
        }
    }
}
