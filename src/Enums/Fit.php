<?php

namespace Spatie\Image\Enums;

enum Fit: string
{
    case Contain = 'contain';
    case Max = 'max';
    case Fill = 'fill';
    case Stretch = 'stretch';
    case Crop = 'crop';
    case FitStretch = 'fitStretch';

    public function shouldResizeCanvas(): bool
    {
        return in_array($this, [self::Max, self::Fill]);
    }
}
