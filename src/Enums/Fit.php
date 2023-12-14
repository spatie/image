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
        ?int $desiredWidth = null,
        ?int $desiredHeight = null,
    ): Size {
        $desiredWidth ??= $originalWidth;
        $desiredHeight ??= $originalHeight;

        $size = new Size($originalWidth, $originalHeight);

        $constraints = match ($this) {
            Fit::Contain, Fit::Max => [Constraint::PreserveAspectRatio],
            Fit::Fill => [Constraint::PreserveAspectRatio, Constraint::DoNotUpsize],
            Fit::Stretch, Fit::Crop => [],
        };

        return $size->resize($desiredWidth, $desiredHeight, $constraints);
    }

    public function shouldResizeCanvas(): bool
    {
        return in_array($this, [self::Max, self::Fill]);
    }
}
