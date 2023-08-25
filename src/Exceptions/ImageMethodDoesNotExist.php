<?php

namespace Spatie\Image\Exceptions;

use Exception;

class ImageMethodDoesNotExist extends Exception
{
    public static function make(string $methodName): static
    {
        return new static("Method `{$methodName}` does not exist");
    }
}
