<?php

namespace Spatie\Image\Exceptions;

use Exception;

class InvalidColor extends Exception
{
    public static function make(mixed $color): self
    {
        return new self("Could not parse color value `{$color}`");
    }

    public static function cannotConvertImagickColorToGd(): self
    {
        return new self('GD colors cannot init from ImagickPixel objects.');
    }
}
