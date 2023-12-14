<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Enums\Orientation;

trait GetsOrientationFromExif
{
    /** @param  array{'Orientation'?: int}  $exif */
    public function getOrientationFromExif(array $exif): Orientation
    {
        if (! isset($exif['Orientation'])) {
            return Orientation::Rotate0;
        }

        return match ($exif['Orientation']) {
            3 => Orientation::Rotate180,
            6 => Orientation::Rotate270,
            8 => Orientation::Rotate90,
            default => Orientation::Rotate0,
        };
    }
}
