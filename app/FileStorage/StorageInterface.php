<?php

namespace Core\FileStorage;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface StorageInterface
{
    /**
     * Create a file storage task.
     * @param \Illuminate\Http\Request $request
     * @return \Core\FileStorage\TaskInterface
     */
    public function createTask(Request $request): TaskInterface;

    /**
     * Get a file info.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @return \Core\FileMetaInterface
     */
    public function meta(ResourceInterface $resource): FileMetaInterface;

    /**
     * Get a file response.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @param string|null $rule
     * @return string
     */
    public function response(ResourceInterface $resource, ?string $rule = null): Response;

    /**
     * Deelte a resource.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @return bool
     */
    public function delete(ResourceInterface $resource): ?bool;

    /**
     * Put a file.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @param mixed $content
     * @return bool
     */
    public function put(ResourceInterface $resource, $content): bool;

    /**
     * A storage task callback handle.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @return void
     */
    public function callback(ResourceInterface $resource): void;
}
