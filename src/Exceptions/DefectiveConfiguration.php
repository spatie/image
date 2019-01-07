<?php

namespace Spatie\Image\Exceptions;

use Exception;

class DefectiveConfiguration extends Exception
{
    public static function invalidTemporaryDirectory($dirPath)
    {
        if (!is_dir($dirPath)) {
            $reason = "is not a directory";
        } elseif (!is_writable($dirPath)) {
            $reason = "the directory is not writable";
        } else {
            $reason = "seems to be corrupt";
        }

        return new self("the temporary directory ${dirPath} is not valid as it ${reason} ");
    }
}