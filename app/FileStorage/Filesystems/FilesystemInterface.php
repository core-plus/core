<?php

namespace Core\FileStorage\Filesystems;

use Illuminate\Http\Request;
use Core\FileStorage\TaskInterface;
use Core\FileStorage\FileMetaInterface;
use Core\FileStorage\ResourceInterface;
use Symfony\Component\HttpFoundation\Response;

interface FilesystemInterface
{
    /**
     * Get file meta.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @return \Core\FileStorage\FileMetaInterface
     */
    public function meta(ResourceInterface $resource): FileMetaInterface;

    /**
     * Get file response.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @param string|null $rule
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(ResourceInterface $resource, ?string $rule = null): Response;

    /**
     * Delete file.
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool;

    /**
     * Create upload task.
     * @param \Illuminate\Http\Request $request
     * @param \Core\FileStorage\ResourceInterface $resource
     * @return \Core\FileStorage\TaskInterface
     */
    public function createTask(Request $request, ResourceInterface $resource): TaskInterface;

    /**
     * Put a file.
     * @param string $path
     * @param mixed $contents
     * @return bool
     */
    public function put(string $path, $contents): bool;
}
