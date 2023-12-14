<?php

namespace Spatie\Image\Drivers\Imagick;

class Helpers
{
    public static function normalizeColorizeLevel(float $level): float
    {
        return $level > 0
            ? $level / 5
            : ($level + 100) / 100;
    }
}
