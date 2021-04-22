<?php

namespace Core\FileStorage;

class ImageDimension implements ImageDimensionInterface
{
    /**
     * The dimension width.
     * @var float
     */
    protected $width;

    /**
     * The dimnsion height.
     * @var float
     */
    protected $height;

    /**
     * Create a image dimension.
     * @param float $width
     * @param float $height
     */
    public function __construct(float $width, float $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get image width (px).
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * Get image height (px).
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }
}
