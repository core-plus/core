<?php

namespace Core\FileStorage;

interface ImageDimensionInterface
{
    /**
     * Get image width (px).
     * @return float
     */
    public function getWidth(): float;

    /**
     * Get image height (px).
     * @return float
     */
    public function getHeight(): float;
}
