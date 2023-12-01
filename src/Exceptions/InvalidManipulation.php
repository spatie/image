<?php

namespace Spatie\Image\Exceptions;

use Exception;

class InvalidManipulation extends Exception
{
    public static function valueNotInRange(string $name, int|float $invalidValue, int|float $minValue, int|float $maxValue): self
    {
        $name = ucfirst($name);

        return new self("{$name} should be a number in the range {$minValue} until {$maxValue}. `{$invalidValue}` given.");
    }
}
