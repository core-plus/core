<?php

namespace Core\FileStorage;

use JsonSerializable;
use Core\Models\User;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Core\FileStorage\Pay\PayInterface;

interface FileMetaInterface extends Arrayable, JsonSerializable, Jsonable
{
    /**
     * Has the file is image.
     * @return bool
     */
    public function hasImage(): bool;

    /**
     * Get image file dimension.
     * @return \Core\FileStorage\ImageDimensionInterface
     */
    public function getImageDimension(): ImageDimensionInterface;

    /**
     * Get the file size (Byte).
     * @return int
     */
    public function getSize(): int;

    /**
     * Get the resource mime type.
     * @return string
     */
    public function getMimeType(): string;

    /**
     * Get the storage vendor name.
     * @return string
     */
    public function getVendorName(): string;

    /**
     * Get the resource pay info.
     * @param \Core\Models\User $user
     * @return \Core\FileStorage\Pay\PayInterface
     */
    public function getPay(User $user): ?PayInterface;

    /**
     * Get the resource url.
     * @return string
     */
    public function url(): string;
}
