<?php

namespace Spatie\Image\Drivers;

use GdImage;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\InvalidManipulation;

class GdImageDriver implements ImageDriver
{
    private GdImage $image;

    public function load(string $path): ImageDriver
    {
        $handle = fopen($path, 'r');
        $contents = fread($handle, filesize($path));
        fclose($handle);

        $image = imagecreatefromstring($contents);

        if (! $image) {
            throw new CouldNotLoadImage("Could not load image from path `{$path}`");
        }

        $this->image = $image;

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

        // Convert value between -100 and 100 to -255 and 255
        $brightness = round($brightness * 2.55);

        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $brightness);

        return $this;
    }

    public function save(string $path): ImageDriver
    {
        // TODO: make this work with other formats.
        imagepng($this->image, $path);

        return $this;
    }
}
