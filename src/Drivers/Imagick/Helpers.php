<?php

namespace Spatie\Image\Drivers\Imagick;

class Helpers
{
    public static function normalizeColorizeLevel(float $level): float
    {
        if ($level > 0) {
            return $level/5;
        } else {
            return ($level+100)/100;
        }
    }
}
