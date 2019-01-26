<?php

namespace Spatie\Image\Exceptions;

use Exception;

class InvalidTemporaryDirectory extends Exception
{
    public static function temporaryDirectoryNotCreatable(string $directory)
    {
        return new self("the temporary directory `{$directory}` does not exist and can not be created");
    }

    public static function temporaryDirectoryNotWritable(string $directory)
    {
        return new self("the temporary directory `{$directory}` does exist but is not writable");
    }
}
