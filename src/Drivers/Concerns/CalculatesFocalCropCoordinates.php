<?php

namespace Spatie\Image\Drivers\Concerns;

/** @mixin \Spatie\Image\Drivers\ImageDriver */
trait CalculatesFocalCropCoordinates
{
    protected function calculateFocalCropCoordinates(int $width, int $height, $cropCenterX, $cropCenterY): array
    {
        $width = min($width, $this->getWidth());
        $height = min($height, $this->getHeight());

        if ($cropCenterX > 0) {
            $maxCropCenterX = $this->getWidth() - $width;
            $cropCenterX = (int) $cropCenterX - ($width / 2);
            $cropCenterX = min($maxCropCenterX, $cropCenterX);
            $cropCenterX = max(0, $cropCenterX);
        }

        if ($cropCenterY > 0) {
            $maxCropCenterY = $this->getHeight() - $height;
            $cropCenterY = (int) $cropCenterY - ($height / 2);
            $cropCenterY = min($maxCropCenterY, $cropCenterY);
            $cropCenterY = max(0, $cropCenterY);
        }

        return [$width, $height, $cropCenterX, $cropCenterY];
    }
}