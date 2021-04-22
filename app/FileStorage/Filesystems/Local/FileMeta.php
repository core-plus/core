<?php

namespace Core\FileStorage\Filesystems\Local;

use Closure;
use Exception;
use Core\Models\User;
use Core\FileStorage\ImageDimension;
use Core\FileStorage\FileMetaAbstract;
use Core\FileStorage\Pay\PayInterface;
use Core\FileStorage\ResourceInterface;
use Core\FileStorage\Traits\HasImageTrait;
use Core\FileStorage\ImageDimensionInterface;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;

class FileMeta extends FileMetaAbstract
{
    use HasImageTrait;

    /**
     * Local filesystem.
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Resource instance.
     * @var \Core\FileStorage\ResourceInterface
     */
    protected $resource;

    /**
     * Cache the instance image dimension.
     * @var \Core\FileStorage\ImageDimensionInterface
     */
    protected $dimension;

    /**
     * Create a file meta.
     * @param \Illuminate\Contracts\Filesystem\Filesystem $filesystem
     * @param \Core\FileStorage\ResourceInterface $resource
     */
    public function __construct(FilesystemContract $filesystem, ResourceInterface $resource)
    {
        $this->filesystem = $filesystem;
        $this->resource = $resource;
        $this->hasImage();
    }

    /**
     * Use custom MIME types.
     * @return null|\Closure
     */
    protected function useCustomTypes(): ?Closure
    {
        return function () {
            return [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
            ];
        };
    }

    /**
     * Has the file is image.
     * @return bool
     */
    public function hasImage(): bool
    {
        return $this->hasImageType(
            $this->getMimeType()
        );
    }

    /**
     * Get image file dimension.
     * @return \Core\FileStorage\ImageDimensionInterface
     */
    public function getImageDimension(): ImageDimensionInterface
    {
        if (! $this->hasImage()) {
            throw new Exception('调用的资源并非图片或者是不支持的图片资源');
        } elseif ($this->dimension instanceof ImageDimensionInterface) {
            return $this->dimension;
        }

        $realPath = $this->filesystem->path(
            $this->resource->getPath()
        );
        [$width, $height] = getimagesize($realPath);

        return $this->dimension = new ImageDimension((float) $width, (float) $height);
    }

    /**
     * Get the file size (Byte).
     * @return int
     */
    public function getSize(): int
    {
        return $this->filesystem->getSize(
            $this->resource->getPath()
        );
    }

    /**
     * Get the resource mime type.
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->filesystem->mimeType(
            $this->resource->getPath()
        ) ?: 'application/octet-stream';
    }

    /**
     * Get the resource pay info.
     * @param \Core\Models\User $user
     * @return \Core\FileStorage\Pay\PayInterface
     */
    public function getPay(User $user): ?PayInterface
    {
        return null;
    }

    /**
     * Get the storage vendor name.
     * @return string
     */
    public function getVendorName(): string
    {
        return 'local';
    }

    /**
     * Get the resource url.
     * @return string
     */
    public function url(): string
    {
        return route('storage:get', [
            'channel' => $this->resource->getChannel(),
            'path' => base64_encode($this->resource->getPath()),
        ]);
    }
}
