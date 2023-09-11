<?php

namespace Spatie\Image\Enums;

enum Orientation
{
    case ROTATE_0;
    case ROTATE_90;
    case ROTATE_180;
    case ROTATE_270;

    public function degrees(): int
    {
        return match ($this) {
            self::ROTATE_0 => 0,
            self::ROTATE_90 => 90,
            self::ROTATE_180 => 180,
            self::ROTATE_270 => 270,
        };
    }
}
