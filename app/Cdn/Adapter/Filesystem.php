<?php

namespace Core\Cdn\Adapter;

use Core\Cdn\Refresh;
use Intervention\Image\Image;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Core\Contracts\Cdn\UrlGenerator as FileUrlGeneratorContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactoryContract;

class Filesystem implements FileUrlGeneratorContract
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The Filesystem instance.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The filesystem disk.
     *
     * @var string
     */
    protected $disk = 'public';

    /**
     * Create the CDN generator.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @author GEO <dev@kaifa.me>
     */
    public function __construct(ApplicationContract $app, FilesystemFactoryContract $files)
    {
        $this->app = $app;
        $this->files = $files->disk(
            $this->disk = config('cdn.generators.filesystem.disk')
        );
    }

    /**
     * Generator a URL.
     *
     * @param string $filename
     * @param array $extra
     * @throws \Exception
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function url(string $filename, array $extra = []): string
    {
        if ($this->files->exists($filename) === false) {
            throw new \Exception("Unable to find a file at path [{$filename}].");
        } elseif (app('files')->extension($filename) === 'gif' && empty($extra)) {
            return $this->makeUrl($filename);
        }

        return $this->validateImageAnd($filename, function (string $filename) use ($extra) {
            return $this->validateProcessAnd($filename, $extra, function (Image $image, array $extra = []) use ($filename) {
                if ($extra['blur']) {
                    $image->blur($extra['blur']);
                }

                $this->processSize($image, $extra);

                $quality = intval($extra['quality'] ?? 90) ?: 90;
                $quality = min($quality, 90);

                $image->encode($image->extension, $quality);

                return $this->putProcessFile(
                    $image,
                    $this->makeProcessFilename($filename, $this->makeProcessFingerprint($extra))
                );
            });
        });
    }

    /**
     * Refresh the cdn files and dirs.
     *
     * @param \Core\Cdn\Refresh $refresh
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    public function refresh(Refresh $refresh)
    {
        $this->files->delete($refresh->getFiles());
        foreach ($refresh->getDirs() as $dir) {
            if ($this->files->exists($dir)) {
                $this->files->deleteDirectory($dir);
            }
        }
    }

    /**
     * ???????????????????????????????????????.
     *
     * @param \Intervention\Image\Image $image
     * @param string $filename
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function putProcessFile(Image $image, string $filename): string
    {
        if (! $image->isEncoded() || ! $this->files->put($filename, $image)) {
            throw new \Exception('The file encode error.');
        }

        return $this->makeUrl($filename);
    }

    /**
     * ??????????????????.
     *
     * @param \Intervention\Image\Image $image
     * @param array $extra
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    protected function processSize(Image $image, array $extra)
    {
        $width = $image->width();
        $height = $image->height();

        $processWidth = floatval($extra['width']);
        $processHeight = floatval($extra['height']);

        if (($width <= $processWidth || $height <= $processHeight) || (! $processWidth && ! $processHeight)) {
            return;
        }

        $minSide = min($processWidth, $processHeight);

        if (($minSide === $processWidth && $processWidth) || ((bool) $processWidth && ! $processHeight)) {
            $image->resize($processWidth, null, function (Constraint $constraint) {
                $constraint->aspectRatio();
            });
        } elseif (($minSide === $processHeight && $processWidth) || ((bool) $processHeight && ! $processWidth)) {
            $image->resize(null, $processHeight, function (Constraint $constraint) {
                $constraint->aspectRatio();
            });
        }
    }

    /**
     * ????????????????????????????????????????????????????????????.
     *
     * @param string $filename
     * @param array $extra
     * @param callable $call
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function validateProcessAnd(string $filename, array $extra, callable $call): string
    {
        $width = floatval($extra['width'] ?? 0.0);
        $height = floatval($extra['height'] ?? 0.0);
        $quality = intval($extra['quality'] ?? 0);
        $blur = intval($extra['blur'] ?? 0);

        if (! $width && ! $height && ! $quality && ! $blur) {
            return $this->makeUrl($filename);
        }

        return $this->validateFingerprint($filename, $call, [
            'width' => $width,
            'height' => $height,
            'quality' => $quality,
            'blur' => $blur,
        ]);
    }

    /**
     * ????????????????????????????????????????????????????????????????????????????????????.
     *
     * @param string $filename
     * @param callable $call
     * @param array $extra
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function validateFingerprint(string $filename, callable $call, array $extra): string
    {
        $processFilename = $this->makeProcessFilename($filename, $this->makeProcessFingerprint($extra));

        if ($this->files->exists($processFilename)) {
            return $this->makeUrl($processFilename);
        }

        return $call(
            $this->makeImage($this->app['config']['filesystems.disks.public.root'].'/'.$filename),
            $extra
        );
    }

    /**
     * Make Image.
     *
     * @param string $filename
     * @return \Intervention\Image\Image
     * @author GEO <dev@kaifa.me>
     */
    protected function makeImage(string $filename): Image
    {
        return $this->app->make(ImageManager::class)->make($filename);
    }

    /**
     * ??????????????????????????????.
     *
     * @param array $extra
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    protected function makeProcessFingerprint(array $extra): string
    {
        return md5(implode('|', array_filter($extra)));
    }

    /**
     * ??????????????????????????????.
     *
     * @param string $filename
     * @param string $fingerprint
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    protected function makeProcessFilename(string $filename, string $fingerprint): string
    {
        $processPath = str_replace(sprintf('.%s', $ext = pathinfo($filename, PATHINFO_EXTENSION)), '/', $filename);

        return $processPath.$fingerprint.'.'.$ext;
    }

    /**
     * ????????????????????? mimeType.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function getSupportMimeTypes(): array
    {
        $mimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
        ];

        if ($this->app['config']['image.driver'] === 'imagick') {
            return array_merge($mimes, [
                'image/tiff',
                'image/bmp',
                'image/x-icon',
                'image/vnd.adobe.photoshop',
            ]);
        }

        return $mimes;
    }

    /**
     * ???????????????????????????????????????????????????.
     *
     * @param string $filename
     * @param callable $call
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    private function validateImageAnd(string $filename, callable $call): string
    {
        if (in_array($this->files->mimeType($filename), $this->getSupportMimeTypes())) {
            return $call($filename);
        }

        return $this->makeUrl($filename);
    }

    /**
     * Make public URL.
     *
     * @param string $filename
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    protected function makeUrl(string $filename): string
    {
        if ($this->disk === 'local') {
            return sprintf('%s/%s', config('cdn.generators.filesystem.public'), $filename);
        }

        return $this->files->url($filename);
    }
}
