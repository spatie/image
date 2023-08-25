<?php

namespace Spatie\Image\Exceptions;

use Exception;

class CouldNotLoadImage extends Exception
{
    public static function make(string $path): static
    {
        return new static("Could not load image at path `{$path}`");
    }

    public static function fileDoesNotExist(string $path): static
    {
        return new static("File does not exist at path `{$path}`");
    }
}
