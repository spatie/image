<?php

namespace Spatie\Image;

use Spatie\Image\Drivers\Gd\GdDriver;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Drivers\Imagick\ImagickDriver;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\ImageMethodDoesNotExist;
use Spatie\Image\Exceptions\InvalidImageDriver;

/** @mixin ImageDriver */
class Image
{
    protected ImageDriver $imageDriver;

    public function __construct(protected string $pathToImage)
    {
        $this->imageDriver = new ImagickDriver();
    }

    public static function load(string $pathToImage): ImageDriver
    {
        if (! file_exists($pathToImage)) {
            throw CouldNotLoadImage::fileDoesNotExist($pathToImage);
        }

        return (new self($pathToImage))->imageDriver->load($pathToImage);
    }

    public static function useImageDriver(string $imageDriverName): ImageDriver
    {
        if (! in_array($imageDriverName, ['gd', 'imagick'])) {
            throw InvalidImageDriver::driver($imageDriverName);
        }

        return match ($imageDriverName) {
            'gd' => new GdDriver(),
            'imagick' => new ImagickDriver(),
        };
    }

    public function __call(string $methodName, array $arguments): static
    {
        if (! method_exists($this->imageDriver, $methodName)) {
            throw ImageMethodDoesNotExist::make($methodName);
        }

        $this->imageDriver->$methodName(...$arguments);

        return $this;
    }
}
