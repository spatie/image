<?php

namespace Spatie\Image\Exceptions;

use Exception;

class DefectiveConfiguration extends Exception
{

    public static function getReason()
    {
        if (!is_dir($dirPath)) {
            return 'is not a directory';
        }

        if (!is_writable($dirPath)) {
            return 'is not writable';
        }

        return 'it seems to be corrupt';
    }

    public static function invalidTemporaryDirectory($dirPath)
    {
        $reason = self::getReason($dirPath);
        return new self("the temporary directory ${dirPath} is not valid as it {$reason} ");
    }
}
