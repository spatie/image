<?php

namespace Spatie\Image\Drivers;

use Imagick;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;

class ImagickImageDriver implements ImageDriver
{
    use ValidatesArguments;

    protected Imagick $image;

    public function load(string $path): ImageDriver
    {
        $this->image = new Imagick($path);

        return $this;
    }

    public function getWidth(): int
    {
        return $this->image->getImageWidth();
    }

    public function getHeight(): int
    {
        return $this->image->getImageHeight();
    }

    public function brightness(int $brightness): ImageDriver
    {
        $this->ensureNumberBetween($brightness, -100, 100, 'brightness');

        $this->image->modulateImage(100 + $brightness, 100, 100);

        return $this;
    }

    public function blur(int $blur): ImageDriver
    {
        $this->ensureNumberBetween($blur, 0, 100, 'blur');

        $this->image->blurImage(1 * $blur, 0.5 * $blur);

        return $this;
    }

    public function save(string $path): ImageDriver
    {
        $this->image->writeImage($path);

        return $this;
    }

    public function driverName(): string
    {
        return 'imagick';
    }
}
