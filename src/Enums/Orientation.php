<?php

namespace Spatie\Image\Enums;

enum Orientation
{
    case Rotate0;
    case Rotate90;
    case Rotate180;
    case Rotate270;

    public function degrees(): int
    {
        return match ($this) {
            self::Rotate0 => 0,
            self::Rotate90 => 90,
            self::Rotate180 => 180,
            self::Rotate270 => 270,
        };
    }
}
