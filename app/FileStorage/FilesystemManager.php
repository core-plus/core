<?php

namespace Core\FileStorage;

use OSS\OssClient;
use Core\AppInterface;
use Illuminate\Support\Manager;
use function Core\setting;

class FilesystemManager extends Manager
{
    /**
     * Create the filesystem manager instance.
     * @param \Core\AppInterface $app
     */
    public function __construct(AppInterface $app)
    {
        parent::__construct($app);
    }

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver()
    {
        return setting('file-storage', 'default-filesystem', 'local');
    }

    /**
     * Create local driver.
     * @return \Core\FileStorage\Filesystems\FilesystemInterface
     */
    public function createLocalDriver(): Filesystems\FilesystemInterface
    {
        $localConfigure = setting('file-storage', 'filesystems.local', [
            'disk' => 'local',
        ]);
        $filesystem = $this
            ->app
            ->make(\Illuminate\Contracts\Filesystem\Factory::class)
            ->disk($localConfigure['disk']);

        return new Filesystems\LocalFilesystem($filesystem);
    }

    /**
     * Create Aliyun OSS filesystem driver.
     * @return \Core\FileStorage\Filesystems\FilesystemInterface
     */
    public function createAliyunOSSDriver(): Filesystems\FilesystemInterface
    {
        $aliyunOssConfigure = setting('file-storage', 'filesystems.aliyun-oss', []);
        $aliyunOssConfigure = array_merge([
            'bucket' => null,
            'access-key-id' => null,
            'access-key-secret' => null,
            'domain' => null,
            'inside-domain' => null,
            'timeout' => 3600,
        ], $aliyunOssConfigure);
        $oss = new OssClient(
            $aliyunOssConfigure['access-key-id'] ?? null,
            $aliyunOssConfigure['access-key-secret'] ?? null,
            $aliyunOssConfigure['domain'] ?? null,
            true
        );
        $insideOss = new OssClient(
            $aliyunOssConfigure['access-key-id'] ?? null,
            $aliyunOssConfigure['access-key-secret'] ?? null,
            $aliyunOssConfigure['inside-domain'] ?? null,
            true
        );

        return new Filesystems\AliyunOssFilesystem($oss, $insideOss, $aliyunOssConfigure);
    }
}
