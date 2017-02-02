<?php

namespace Spatie\Image\Exceptions;

use Exception;

class CouldNotConvert extends Exception
{
    public static function unknownManipulation(string $operationName): self
    {
        return new self("Can not convert image. Unknown operation `{$operationName}` used");
    }
}
