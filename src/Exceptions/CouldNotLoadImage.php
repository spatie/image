<?php

namespace Spatie\Image\Exceptions;

use Exception;

class CouldNotLoadImage extends Exception
{
    public static function make(string $path): static
    {
        return new static("Could not load image from path `{$path}`");
    }
}
