<?php

namespace Spatie\Image;

use BadMethodCallException;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Drivers\ImagickImage;
use Spatie\Image\Exceptions\InvalidImageDriver;

/** @mixin ImageDriver */
class Image
{
    protected ImageDriver $imageDriver;

    public function __construct(protected string $pathToImage)
    {
        $this->imageDriver = new ImagickImage();
    }

    public static function load(string $pathToImage): ImageDriver
    {
        return (new self($pathToImage))->imageDriver->load($pathToImage);
    }

    /**
     * @return $this
     *
     * @throws InvalidImageDriver
     */
    public function useImageDriver(string $imageDriverName): static
    {
        if (! in_array($imageDriverName, ['gd', 'imagick'])) {
            throw InvalidImageDriver::driver($imageDriverName);
        }

        $this->imageDriver = match ($imageDriverName) {
            'gd' => new Drivers\GdImageDriver(),
            'imagick' => new Drivers\ImagickImage(),
        };

        return $this;
    }

    public function __call(string $name, array $arguments): static
    {
        if (! method_exists($this->imageDriver, $name)) {
            throw new BadMethodCallException("Manipulation `{$name}` does not exist");
        }

        $this->imageDriver->$name(...$arguments);

        return $this;
    }
}
