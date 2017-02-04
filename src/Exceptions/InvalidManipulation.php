<?php

namespace Spatie\Image\Exceptions;

use Exception;

class InvalidManipulation extends Exception
{
    public static function invalidWidth(int $width)
    {
        return new self("Width should be a positive number. `{$width}` given");
    }

    public static function invalidOrientation($orientation, array $validValues)
    {
        $validValues = self::formatValues($validValues);

        return new self("Orientation should be one of {$validValues}. `{$orientation}` given");
    }

    protected static function formatValues(array $values): string
    {
        $quotedValues = array_map(function(string $value) {
            return "`{$value}`";
        }, $values);

        return implode(', ', $quotedValues);

    }
}