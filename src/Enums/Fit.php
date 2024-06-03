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

    case FillMax = 'fill-max';

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
            Fit::Contain => [Constraint::PreserveAspectRatio],
            Fit::Fill, Fit::Max, Fit::FillMax => [Constraint::PreserveAspectRatio, Constraint::DoNotUpsize],
            Fit::Stretch, Fit::Crop => [],
        };

        return $size->resize($desiredWidth, $desiredHeight, $constraints);
    }

    public function shouldResizeCanvas(): bool
    {
        return in_array($this, [self::Fill, self::FillMax]);
    }
}
