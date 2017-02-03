<?php

namespace Spatie\Image\Exceptions;

use Exception;

class InvalidManipulation extends Exception
{
    public static function invalidWidth(int $width)
    {
        return new self("Width should be a positive number. {$width} given");
    }
}