<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Enums\Orientation;

trait GetsOrientationFromExif
{
    public function getOrientationFromExif(array $exif): Orientation
    {
        if (! isset($exif['Orientation'])) {
            return Orientation::ROTATE_0;
        }

        return match ($exif['Orientation']) {
            3 => Orientation::ROTATE_180,
            6 => Orientation::ROTATE_270,
            8 => Orientation::ROTATE_90,
            default => Orientation::ROTATE_0,
        };
    }
}
