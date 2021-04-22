<?php

namespace Core\FileStorage\Channels;

use Illuminate\Http\Request;
use Core\FileStorage\TaskInterface;
use Core\FileStorage\FileMetaInterface;
use Core\FileStorage\ResourceInterface;
use Symfony\Component\HttpFoundation\Response;
use Core\FileStorage\Filesystems\FilesystemInterface;

interface ChannelInterface
{
    /**
     * Set resource.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @return void
     */
    public function setResource(ResourceInterface $resource): void;

    /**
     * Set request.
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function setRequest(Request $request): void;

    /**
     * Set filesystem.
     * @param \Core\FileStorage\Filesystems\FilesystemInterface $filesystem
     * @return void
     */
    public function setFilesystem(FilesystemInterface $filesystem): void;

    /**
     * Create a upload task.
     * @return \Core\FileStorage\TaskInterface
     */
    public function createTask(): TaskInterface;

    /**
     * Get a resource meta.
     * @return \Core\FileStorage\FileMetaInterface
     */
    public function meta(): FileMetaInterface;

    /**
     * Get a resource response.
     * @param string|null $rule
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(?string $rule = null): Response;

    /**
     * Uploaded callback handler.
     * @return void
     */
    public function callback(): void;

    /**
     * Put a file.
     * @param mixed $contents
     * @return vodi
     */
    public function put($contents): bool;
}
