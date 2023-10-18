<?php

namespace Spatie\Image;

use Spatie\Image\Drivers\Gd\GdDriver;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Drivers\Imagick\ImagickDriver;
use Spatie\Image\Enums\ImageDriver as ImageDriverEnum;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\InvalidImageDriver;

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

    public static function useImageDriver(ImageDriverEnum|string $imageDriver): ImageDriver
    {
        if (is_string($imageDriver)) {
            $imageDriver = ImageDriverEnum::tryFrom($imageDriver)
                ?? throw InvalidImageDriver::driver($imageDriver);
        }

        return match ($imageDriver) {
            ImageDriverEnum::Gd => new GdDriver(),
            ImageDriverEnum::Imagick => new ImagickDriver(),
        };
    }
}
