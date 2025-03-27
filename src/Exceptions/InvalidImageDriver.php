<?php

namespace Spatie\Image\Exceptions;

use Exception;

class InvalidImageDriver extends Exception
{
    public static function driver(string $driver): self
    {
        return new self("Driver must be `gd`, `imagick`, or an implementation of `Spatie\Image\Drivers\ImageDriver`. `{$driver}` provided.");
    }
}
