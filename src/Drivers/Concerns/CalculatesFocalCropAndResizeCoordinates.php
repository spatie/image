<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Image;

trait CalculatesFocalCropAndResizeCoordinates
{
    /** @return array{int, int, int, int} */
    public function calculateFocalCropAndResizeCoordinates(
        ?int $desiredWidth,
        ?int $desiredHeight,
        ?float $cropCenterX = 50.0,
        ?float $cropCenterY = 50.0,
    ): array {
        $originalWidth = $this->getWidth();
        $originalHeight = $this->getHeight();

        $targetRatio = $desiredWidth / $desiredHeight;
        $originalRatio = $originalWidth / $originalHeight;

        if ($originalRatio > $targetRatio) {
            // Image is wider, crop width
            $cropHeight = $originalHeight;
            $cropWidth = (int) round($cropHeight * $targetRatio);
        } else {
            // Image is taller, crop height
            $cropWidth = $originalWidth;
            $cropHeight = (int) round($cropWidth / $targetRatio);
        }

        // Focal point in pixels
        $focalX = $originalWidth * ($cropCenterX / 100);
        $focalY = $originalHeight * ($cropCenterY / 100);

        // Top-left crop coordinates
        $cropX = max(0, min((int) ($focalX - $cropWidth / 2), $originalWidth - $cropWidth));
        $cropY = max(0, min((int) ($focalY - $cropHeight / 2), $originalHeight - $cropHeight));

        return [$cropWidth, $cropHeight, $cropX, $cropY];
    }
}
