<?php

namespace Spatie\Image\Drivers;

use Imagick;
use Spatie\Image\Exceptions\InvalidManipulation;

class ImagickImageDriver implements ImageDriver
{
    protected Imagick $image;

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
        if ($brightness < -100 || $brightness > 100) {
            throw InvalidManipulation::valueNotInRange('brightness', $brightness, -100, 100);
        }

        $this->image->modulateImage(100 + $brightness, 100, 100);

        return $this;
    }

    public function save(string $path): ImageDriver
    {
        $this->image->writeImage($path);

        return $this;
    }
}
