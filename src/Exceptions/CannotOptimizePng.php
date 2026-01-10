<?php

namespace Spatie\Image\Exceptions;

use Exception;

class CannotOptimizePng extends Exception
{
    public static function make(): self
    {
        return new self('The vips driver cannot optimize pngs');
    }
}
