<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Fit;

trait PerformsFitCrops
{
    abstract public function height(int $height): static;

    abstract public function width(int $width): static;

    abstract public function resizeCanvas(int $width, int $height, AlignPosition $position): static;

    public function fitCrop(
        Fit $fit,
        int $originalWidth,
        int $originalHeight,
        ?int $desiredWidth = null,
        ?int $desiredHeight = null
    ): static {
        $desiredWidth ??= $originalWidth;
        $desiredHeight ??= $originalHeight;

        if ($originalWidth < $desiredWidth || $originalHeight < $desiredHeight) {
            if ($desiredWidth < $desiredHeight) {
                $this->height($desiredHeight);
            } else {
                $this->width($desiredWidth);
            }
        }

        $currentAspectRatio = $originalWidth / $originalHeight;
        $desiredAspectRatio = $desiredWidth / $desiredHeight;

        if ($currentAspectRatio > $desiredAspectRatio) {
            $this->height($desiredHeight);
        } else {
            $this->width($desiredWidth);
        }

        $this->resizeCanvas($desiredWidth, $desiredHeight, AlignPosition::Center);

        return $this;
    }
}
