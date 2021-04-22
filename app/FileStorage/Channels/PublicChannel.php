<?php

namespace Core\FileStorage\Channels;

use Core\FileStorage\TaskInterface;
use Core\FileStorage\FileMetaInterface;

class PublicChannel extends AbstractChannel
{
    /**
     * Create a upload task.
     * @return \Core\FileStorage\TaskInterface
     */
    public function createTask(): TaskInterface
    {
        return $this->filesystem->createTask($this->request, $this->resource);
    }

    /**
     * Get a resource meta.
     * @return \Core\FileStorage\FileMetaInterface
     */
    public function meta(): FileMetaInterface
    {
        return $this->filesystem->meta($this->resource);
    }

    /**
     * Uploaded callback handler.
     * @return void
     */
    public function callback(): void
    {
    }
}
