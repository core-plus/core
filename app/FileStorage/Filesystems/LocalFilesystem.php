<?php

namespace Core\FileStorage\Filesystems;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use function Core\setting;
use Core\FileStorage\Task;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Core\FileStorage\TaskInterface;
use Core\FileStorage\FileMetaInterface;
use Core\FileStorage\ResourceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;

class LocalFilesystem implements FilesystemInterface
{
    /**
     * The local filesystem.
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Cache the file metas.
     * @var array<\Core\FileStorage\FileMetaInterface>
     */
    protected $metas = [];

    /**
     * Create the filesystem driver instance.
     * @param \\Illuminate\Contracts\Filesystem\Filesystem $folesystem
     */
    public function __construct(FilesystemContract $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Get file meta.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @return \Core\FileStorage\FileMetaInterface
     */
    public function meta(ResourceInterface $resource): FileMetaInterface
    {
        $resourceString = (string) $resource;
        $meta = $this->metas[$resourceString] ?? null;

        if ($meta instanceof FileMetaInterface) {
            return $meta;
        }

        return $this->metas[$resourceString] = new Local\FileMeta($this->filesystem, $resource);
    }

    /**
     * Get file response.
     * @param \Core\FileStorage\ResourceInterface $resource
     * @param string|null $rule
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(ResourceInterface $resource, ?string $rule = null): Response
    {
        $realPath = $this->filesystem->path($resource->getPath());
        if ($this->meta($resource)->hasImage()) {
            $rule = new Local\RuleParser($rule);
            if (
                $rule->getQuality() >= 90 &&
                ! $rule->getBlur() &&
                ! $rule->getWidth() &&
                ! $rule->getHeight() &&
                strtolower($this->meta($resource)->getMimeType()) === 'image/gif'
            ) {
                return $this->filesystem->response($resource->getPath());
            }

            $pathinfo = \League\Flysystem\Util::pathinfo($resource->getPath());
            $cachePath = sprintf('%s/%s/%s.%s', $pathinfo['dirname'], $pathinfo['filename'], $rule->getFilename(), $pathinfo['extension']);
            if ($this->filesystem->has($cachePath)) {
                return $this->filesystem->response($cachePath);
            }

            $image = Image::make($realPath);
            $image->blur($rule->getBlur());
            if (($image->width() > $rule->getWidth() || $image->height() > $rule->getHeight()) && ($rule->getWidth() || $rule->getHeight())) {
                $image->resize($rule->getWidth() ?: null, $rule->getHeight() ?: null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            $contents = $image->encode($image->extension, $rule->getQuality());
            $this->filesystem->put($cachePath, $contents);

            return $image->response();
        }

        return new BinaryFileResponse($realPath);
    }

    /**
     * Delete file.
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        $pathinfo = \League\Flysystem\Util::pathinfo($path);
        $dir = sprintf('%s/%s', $pathinfo['dirname'], $pathinfo['filename']);

        $this->filesystem->deleteDir($dir);
        $this->filesystem->delete($path);

        return true;
    }

    /**
     * Create upload task.
     * @param \Illuminate\Http\Request $request
     * @param \Core\FileStorage\ResourceInterface $resource
     * @return \Core\FileStorage\TaskInterface
     */
    public function createTask(Request $request, ResourceInterface $resource): TaskInterface
    {
        $expiresAt = (new Carbon)->addSeconds(
            setting('file-storage', 'filesystems.local', [])['timeout'] ?? 3600
        );
        $uri = url()->temporarySignedRoute('storage:local-put', $expiresAt, [
            'channel' => $resource->getChannel(),
            'path' => base64_encode($resource->getPath()),
        ]);
        $user = $this->guard()->user();

        return new Task($resource, $uri, 'PUT', null, null, [
            'Authorization' => 'Bearer '.$this->guard()->login($user),
            'x-packages-storage-hash' => $request->input('hash'),
            'x-packages-storage-size' => $request->input('size'),
            'x-packages-storage-mime-type' => $request->input('mime_type'),
        ]);
    }

    /**
     * Put a file.
     * @param string $path
     * @param mixed $contents
     * @return bool
     */
    public function put(string $path, $contents): bool
    {
        return (bool) $this->filesystem->put($path, $contents);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard(): Guard
    {
        return Auth::guard('api');
    }
}
