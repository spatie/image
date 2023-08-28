<?php

namespace Spatie\Image\Drivers;

use GdImage;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\InvalidManipulation;

class GdImageDriver implements ImageDriver
{
    use ValidatesArguments;

    private GdImage $image;

    public function load(string $path): ImageDriver
    {
        $handle = fopen($path, 'r');

        $contents = fread($handle, filesize($path));

        fclose($handle);

        $image = imagecreatefromstring($contents);

        if (! $image) {
            throw CouldNotLoadImage::make($path);
        }

        $this->image = $image;

        return $this;
    }

    public function getWidth(): int
    {
        return imagesx($this->image);
    }

    public function getHeight(): int
    {
        return imagesy($this->image);
    }

    public function brightness(int $brightness): ImageDriver
    {
        $this->ensureNumberBetween($brightness, -100, 100, 'brightness');

        // TODO: Convert value between -100 and 100 to -255 and 255
        $brightness = round($brightness * 2.55);

        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $brightness);

        return $this;
    }

    public function blur(int $blur): ImageDriver
    {
        $this->ensureNumberBetween($blur, 0, 100, 'blur');

        for ($i=0; $i < $blur; $i++) {
            imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
        }

        return $this;
    }

    public function save(string $path): ImageDriver
    {
        // TODO: make this work with other formats.
        imagepng($this->image, $path);

        return $this;
    }

    public function driverName(): string
    {
        return 'gd';
    }
}
