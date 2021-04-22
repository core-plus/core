<?php

namespace Core\Models\Concerns;

use Image;
use Illuminate\Support\Arr;
use Core\Cdn\Refresh;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Filesystem\FilesystemManager;
use Core\Contracts\Cdn\UrlFactory as CdnUrlFactoryContract;

trait HasAvatar
{
    /**
     * Get avatar trait.
     *
     * @return string|int
     * @author GEO <dev@kaifa.me>
     */
    abstract public function getAvatarKey(): string;

    /**
     * avatar extensions.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function getAvatarExtensions(): array
    {
        return ['svg', 'png', 'jpg', 'jpeg', 'gif', 'bmp'];
    }

    /**
     * Avatar prefix.
     *
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function getAvatarPrefix(): string
    {
        return 'avatars';
    }

    /**
     * Get avatar,.
     *
     * @param int $size
     * @param string $prefix
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function avatar(int $size = 0, string $prefix = '')
    {
        $path = $this->avatarPath($prefix);

        if (! $path) {
            return null;
        }

        return app(CdnUrlFactoryContract::class)->generator()->url($path, $size ? [
            'width' => $size,
            'height' => $size,
        ] : []);
    }

    /**
     * Get avatar file path.
     *
     * @param string $prefix
     * @return string|null
     * @author GEO <dev@kaifa.me>
     */
    public function avatarPath(string $prefix = '')
    {
        $path = $this->makeAvatarPath($prefix);
        $disk = $this->filesystem()->disk(
            config('cdn.generators.filesystem.disk')
        );

        foreach ($this->getAvatarExtensions() as $extension) {
            if ($disk->exists($filename = $path.'.'.$extension)) {
                return $filename;
            }
        }

        return null;
    }

    /**
     * Store avatar.
     *
     * @param UploadedFile $avatar
     * @return string|false
     * @author GEO <dev@kaifa.me>
     */
    public function storeAvatar(UploadedFile $avatar, string $prefix = '')
    {
        $prefix = $prefix ?: $this->getAvatarPrefix();
        $extension = strtolower($avatar->extension());
        if (! in_array($extension, $this->getAvatarExtensions())) {
            throw new \Exception('保存的头像格式不符合要求');
        }
        if ($extension !== 'gif') {
            ini_set('memory_limit', '-1');
            Image::make($avatar->getRealPath())->orientate()->save($avatar->getRealPath(), 100);
        }

        $filename = $this->makeAvatarPath($prefix);
        $path = pathinfo($filename, PATHINFO_DIRNAME);
        $name = pathinfo($filename, PATHINFO_BASENAME).'.'.$extension;

        $files = array_reduce($this->getAvatarExtensions(), function (array $collect, $extension) use ($filename) {
            $collect[] = $filename.'.'.$extension;

            return $collect;
        }, []);
        app(CdnUrlFactoryContract::class)->generator()->refresh(new Refresh($files, [$filename]));
        // 头像更新时间
        $now = new Carbon();
        Cache::forever('avatar_'.$this->id.$prefix.'_lastModified_at', $now->timestamp);

        return $avatar->storeAs($path, $name, config('cdn.generators.filesystem.disk'));
    }

    /**
     * make avatar file path.
     *
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    protected function makeAvatarPath(string $prefix = ''): string
    {
        $filename = strval($this->getAvatarKey());
        if (strlen($filename) < 11) {
            $filename = str_pad($filename, 11, '0', STR_PAD_LEFT);
        }

        return sprintf(
            '%s/%s/%s/%s/%s',
            $prefix ?: $this->getAvatarPrefix(),
            substr($filename, 0, 3),
            substr($filename, 3, 3),
            substr($filename, 6, 3),
            substr($filename, 9)
        );
    }

    /**
     *  Get filesystem.
     *
     * @return \Illuminate\Filesystem\FilesystemManager
     * @author GEO <dev@kaifa.me>
     */
    protected function filesystem(): FilesystemManager
    {
        return app(FilesystemManager::class);
    }
}
