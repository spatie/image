<?php

namespace Spatie\Image\Enums;

use Spatie\Image\Size;

enum Fit: string
{
    case Contain = 'contain';
    case Max = 'max';
    case Fill = 'fill';
    case Stretch = 'stretch';
    case Crop = 'crop';

    public function calculateSize(
        int $originalWidth,
        int $originalHeight,
        int $desiredWidth = null,
        int $desiredHeight = null,
    ): Size {
        $desiredWidth ??= $originalWidth;
        $desiredHeight ??= $originalHeight;

        $size = new Size($originalWidth, $originalHeight);

        return match ($this) {
            Fit::Contain => $size->resize($desiredWidth, $desiredHeight, [Constraint::PreserveAspectRatio]),
            Fit::Fill => $size->resize($desiredWidth, $desiredHeight, [Constraint::PreserveAspectRatio, Constraint::DoNotUpsize]),
            Fit::Max => $size->resize($desiredWidth, $desiredHeight, [Constraint::PreserveAspectRatio]),
            Fit::Stretch => $size->resize($desiredWidth, $desiredHeight),
        };
    }

    public function shouldResizeCanvas(): bool
    {
        return in_array($this, [self::Max, self::Fill]);
    }
}
