<?php

namespace Core\FileStorage;

abstract class FileMetaAbstract implements FileMetaInterface
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $baseArr = [
            'url' => $this->url(),
            'vendor' => $this->getVendorName(),
            'mime' => $this->getMimeType(),
            'size' => $this->getSize(),
        ];
        if ($this->hasImage()) {
            $baseArr['dimension'] = [
                'width' => $this->getImageDimension()->getWidth(),
                'height' => $this->getImageDimension()->getHeight(),
            ];
        }

        return $baseArr;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the object to its JSON representation.
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->toJson();
    }
}
