<?php

namespace Spatie\Image\Exceptions;

use Exception;

class UnsupportedImageFormat extends Exception
{
    public static function make(string $extension): self
    {
        return new self("Unsupported format `{$extension}`.");
    }
}
