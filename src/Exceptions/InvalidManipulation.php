<?php

namespace Spatie\Image\Exceptions;

use Exception;

class InvalidManipulation extends Exception
{
    public static function invalidWidth(int $width): self
    {
        return new self("Width should be a positive number. `{$width}` given.");
    }

    public static function invalidHeight(int $height): self
    {
        return new self("Height should be a positive number. `{$height}` given.");
    }

    public static function valueNotInRange(string $name, $invalidValue, $minValue, $maxValue): self
    {
        $name = ucfirst($name);

        return new self("{$name} should be a number in the range {$minValue} until {$maxValue}. `{$invalidValue}` given.");
    }
}
