<?php

namespace Spatie\Image\Drivers;

use Imagick;
use Spatie\Image\Actions\CalculateFitSizeAction;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Size;

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

    public function fit(Fit $fit, int $desiredWidth = null, int $desiredHeight = null): ImageDriver
    {
        $resize = (new CalculateFitSizeAction())->execute(
            $this->getWidth(),
            $this->getHeight(),
            $fit,
            $desiredWidth,
            $desiredHeight,
        );

        $this->image->scaleImage($resize->width, $resize->height);

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

    public function getSize(): Size
    {
        return new Size($this->getWidth(), $this->getHeight());
    }

    public function gamma(float $gamma): ImageDriver
    {
        $this->ensureNumberBetween($gamma, 0.1, 9.99, 'gamma');

        $this->image->gammaImage($gamma);

        return $this;
    }

    public function contrast(float $level): ImageDriver
    {
        $this->ensureNumberBetween($level, -100, 100, 'contrast');

        $this->image->brightnessContrastImage(1, $level);

        return $this;
    }
}
