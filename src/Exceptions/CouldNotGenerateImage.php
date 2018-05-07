<?php

namespace Spatie\Image\Exceptions;

use Exception;
use Throwable;

class CouldNotGenerateImage extends Exception
{
    public static function fromError(Throwable $error): self
    {
        return new self("Could not generate image becaus of underlying error: {$error->getMessage()}");
    }
}
