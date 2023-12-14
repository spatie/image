<?php

namespace Spatie\Image\Exceptions;

use Exception;

class CannotResize extends Exception
{
    public static function invalidWidth(): self
    {
        return new self('This resize would result in width being 0.');
    }

    public static function invalidHeight(): self
    {
        return new self('This resize would result in height being 0.');
    }
}
