<?php

namespace Core\FileStorage\Filesystems\AliyunOss;

use Closure;
use OSS\OssClient;
use OSS\Core\MimeTypes;
use Core\Models\User;
use Core\FileStorage\ImageDimension;
use Core\FileStorage\FileMetaAbstract;
use Core\FileStorage\Pay\PayInterface;
use Core\FileStorage\ResourceInterface;
use Core\FileStorage\Traits\HasImageTrait;
use Core\FileStorage\ImageDimensionInterface;

class FileMeta extends FileMetaAbstract
{
    use HasImageTrait;

    protected $oss;
    protected $resource;
    protected $bucket;
    protected $dimension;
    protected $metaData;

    /**
     * Create a file meta.
     * @param \OSS\OssClient $oss
     * @param \Core\FileStorage\ResourceInterface $resource
     * @param string $bucket
     */
    public function __construct(OssClient $oss, ResourceInterface $resource, string $bucket)
    {
        $this->oss = $oss;
        $this->resource = $resource;
        $this->bucket = $bucket;
        $this->getSize();
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

        $meta = $this->getFileMeta();

        return new ImageDimension(
            (float) $meta->ImageWidth->value,
            (float) $meta->ImageHeight->value
        );
    }

    /**
     * Get the file size (Byte).
     * @return int
     */
    public function getSize(): int
    {
        $meta = $this->getFileMeta();

        return (int) ($meta->FileSize->value ?? $meta->{'content-length'});
    }

    /**
     * Get the resource mime type.
     * @return string
     */
    public function getMimeType(): string
    {
        return MimeTypes::getMimetype($this->resource->getPath()) ?: 'application/octet-stream';
    }

    /**
     * Get the storage vendor name.
     * @return string
     */
    public function getVendorName(): string
    {
        return 'aliyun-oss';
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

    /**
     * Custom using MIME types.
     * @return null\Closure
     */
    protected function useCustomTypes(): ?Closure
    {
        return function () {
            return [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/bmp',
                'image/tiff',
                'image/webp',
            ];
        };
    }

    protected function getFileMeta(): object
    {
        if (! $this->metaData) {
            if (! $this->hasImage()) {
                $this->metaData = Cache::rememberForever((string) $this->resource, function () {
                    return (object) $this->oss->getObjectMeta($this->bucket, $this->resource->getPath());
                });
            }

            $this->metaData = Cache::rememberForever((string) $this->resource, function () {
                $url = $this->oss->signUrl($this->bucket, $this->resource->getPath(), 3600, 'GET', [
                    OssClient::OSS_PROCESS => 'image/info',
                ]);
                $result = file_get_contents($url);

                return json_decode($result, false);
            });
        }

        return $this->metaData;
    }
}
