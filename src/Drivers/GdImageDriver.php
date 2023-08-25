<?php

namespace Spatie\Image\Drivers;

use GdImage;
use Spatie\Image\Exceptions\CouldNotLoadImage;

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
