<?php

namespace Spatie\Image\Exceptions;

use Exception;

class InvalidTemporaryDirectory extends Exception
{
    public static function temporaryDirectoryNotCreatable($dirPath)
    {
        return new self("the temporary directory ${dirPath} does not exist and can not be created");
    }

    public static function temporaryDirectoryNotWritable($dirPath)
    {
        return new self("the temporary directory ${dirPath} does exist but is not writable");
    }
}
