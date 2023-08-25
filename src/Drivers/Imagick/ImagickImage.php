<?php

namespace Spatie\Image\Drivers\Imagick;

use Imagick;
use Spatie\Image\Drivers\ImageDriver;

class ImagickImage implements ImageDriver
{
    private Imagick $image;

    public function load(string $path): ImageDriver
    {
        $this->image = new Imagick($path);

        return $this;
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
        $this->image->modulateImage(100 + $brightness, 100, 100);

        return $this;
    }

    public function save(string $path): ImageDriver
    {
        $this->image->writeImage($path);

        return $this;
    }
}
