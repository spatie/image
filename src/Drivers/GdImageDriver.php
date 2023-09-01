<?php

namespace Spatie\Image\Drivers;

use GdImage;
use Spatie\Image\Actions\CalculateFitSizeAction;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Size;

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

        for ($i = 0; $i < $blur; $i++) {
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

    public function getSize(): Size
    {
        return new Size($this->getWidth(), $this->getHeight());
    }

    public function fit(Fit $fit, int $desiredWidth = null, int $desiredHeight = null): ImageDriver
    {
        $resize = (new CalculateFitSizeAction())->execute(
            $this->getWidth(),
            $this->getHeight(),
            $fit,
            $desiredWidth,
            $desiredHeight,
        );

        $this->modify($this->getWidth(), $this->getHeight(), $resize->width, $resize->height);

        return $this;
    }

    protected function modify($originalWidth, $originalHeight, $desiredWidth, $desiredHeight)
    {
        // create new image
        $modified = imagecreatetruecolor(intval($desiredWidth), intval($desiredHeight));

        // preserve transparency
        $transIndex = imagecolortransparent($this->image);

        if ($transIndex != -1) {
            $rgba = imagecolorsforindex($modified, $transIndex);
            $transColor = imagecolorallocatealpha($modified, $rgba['red'], $rgba['green'], $rgba['blue'], 127);
            imagefill($modified, 0, 0, $transColor);
            imagecolortransparent($modified, $transColor);
        } else {
            imagealphablending($modified, false);
            imagesavealpha($modified, true);
        }

        // copy content from resource
        $result = imagecopyresampled(
            $modified,
            $this->image,
            0,
            0,
            0,
            0,
            intval($desiredWidth),
            intval($desiredHeight),
            $originalWidth,
            $originalHeight
        );

        $this->image = $modified;

        return $result;
    }

    public function gamma(float $gamma): ImageDriver
    {
        $this->ensureNumberBetween($gamma, 0.1, 9.99, 'gamma');

        imagegammacorrect($this->image, 1, $gamma);

        return $this;
    }
}
