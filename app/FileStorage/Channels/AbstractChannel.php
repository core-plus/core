<?php

namespace Core\FileStorage\Channels;

use Illuminate\Http\Request;
use Core\FileStorage\ResourceInterface;
use Symfony\Component\HttpFoundation\Response;
use Core\FileStorage\Filesystems\FilesystemInterface;

abstract class AbstractChannel implements ChannelInterface
{
    /**
     * The resource.
     * @var \Core\FileStorage\ResourceInterface
     */
    protected $resource;

    /**
     * A request instance.
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Filesystem.
     * @var \Core\FileStorage\Filesystems\FilesystemInterface
     */
    protected $filesystem;

    /**
     * Set request.
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * Set resource.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @return void
     */
    public function setResource(ResourceInterface $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * Set filesystem.
     * @param \Core\FileStorage\Filesystems\FilesystemInterface $filesystem
     * @return void
     */
    public function setFilesystem(FilesystemInterface $filesystem): void
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Put a file.
     * @param mixed $contents
     * @return vodi
     */
    public function put($contents): bool
    {
        return $this->filesystem->put($this->resource->getPath(), $contents);
    }

    /**
     * Get a resource response.
     * @param string|null $rule
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(?string $rule = null): Response
    {
        return $this->filesystem->response($this->resource, $rule);
    }
}
