<?php

namespace Spatie\Image\Exceptions;

use Exception;

class InvalidFont extends Exception
{
    public static function make(mixed $font): self
    {
        return new self("Could not find a font file at `{$font}`");
    }
}
