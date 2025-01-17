<?php

namespace Spatie\Image\Exceptions;

use Exception;

class UnsupportedImageFormat extends Exception
{
    public static function make(string $extension, ?\Throwable $previous = null): self
    {
        return new self("Unsupported format `{$extension}`.", previous: $previous);
    }
}
