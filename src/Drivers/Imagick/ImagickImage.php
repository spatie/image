<?php

namespace Spatie\Image\Drivers\Imagick;

use Spatie\Image\Drivers\ImageDriver;

class ImagickImage implements ImageDriver
{

    public static function load(string $path): ImageDriver
    {
        // TODO: Implement load() method.
    }

    public function getWidth(): int
    {
        // TODO: Implement getWidth() method.
    }

    public function getHeight(): int
    {
        // TODO: Implement getHeight() method.
    }

    public function brightness(int $brightness): ImageDriver
    {
        // TODO: Implement brightness() method.
    }
}
