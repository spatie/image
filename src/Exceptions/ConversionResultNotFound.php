<?php

namespace Spatie\Image\Exceptions;

use Exception;

class ConversionResultNotFound extends Exception
{
    public static function notInitialised(): self
    {
        return new self("No manipulations were performed yet.");
    }
}
