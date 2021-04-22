<?php

namespace Core\FileStorage\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Core\FileStorage\Resource;
use Core\FileStorage\StorageInterface;

class Callback
{
    /**
     * File storage instance.
     * @var \Core\FileStorage\StorageInterface
     */
    protected $storage;

    /**
     * Create the controller instance.
     * @param \Core\FileStorage\StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Callcack handler.
     * @param string $channel
     * @param string $path
     * @return \Illuminate\Http\JsonResponse;
     */
    public function __invoke(string $channel, string $path): JsonResponse
    {
        $resource = new Resource($channel, base64_decode($path));
        $this->storage->callback($resource);

        return new JsonResponse(['node' => (string) $resource], JsonResponse::HTTP_OK);
    }
}
