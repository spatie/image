<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Enums\CropPosition;

/** @mixin \Spatie\Image\Drivers\ImageDriver */
trait CalculatesCropOffsets
{
    /** @return array<int> */
    protected function calculateCropOffsets(int $width, int $height, CropPosition $position): array
    {
        [$offsetPercentageX, $offsetPercentageY] = $position->offsetPercentages();

        $offsetX = (int) (($this->getWidth() * $offsetPercentageX / 100) - ($width / 2));
        $offsetY = (int) (($this->getHeight() * $offsetPercentageY / 100) - ($height / 2));

        $maxOffsetX = $this->getWidth() - $width;
        $maxOffsetY = $this->getHeight() - $height;

        if ($offsetX < 0) {
            $offsetX = 0;
        }

        if ($offsetY < 0) {
            $offsetY = 0;
        }

        if ($offsetX > $maxOffsetX) {
            $offsetX = $maxOffsetX;
        }

        if ($offsetY > $maxOffsetY) {
            $offsetY = $maxOffsetY;
        }

        return [$offsetX, $offsetY];
    }
}
