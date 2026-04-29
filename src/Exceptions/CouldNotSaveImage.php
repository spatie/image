<?php

namespace Spatie\Image\Exceptions;

use Exception;

class CouldNotSaveImage extends Exception
{
    public static function failedToWriteToPath(string $path): static
    {
        return new static("Could not write image to path `{$path}`");
    }

    public static function failedToRename(string $from, string $to): static
    {
        return new static("Could not rename `{$from}` to `{$to}`");
    }
}
