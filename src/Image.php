<?php

namespace Spatie\Image;

use BadMethodCallException;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Drivers\ImagickImageDriver;
use Spatie\Image\Exceptions\InvalidImageDriver;

/** @mixin ImageDriver */
class Image
{
    protected ImageDriver $imageDriver;

    public function __construct(protected string $pathToImage)
    {
        $this->imageDriver = new ImagickImageDriver();
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
    public static function useImageDriver(string $imageDriverName): ImageDriver
    {
        if (! in_array($imageDriverName, ['gd', 'imagick'])) {
            throw InvalidImageDriver::driver($imageDriverName);
        }

        return match ($imageDriverName) {
            'gd' => new Drivers\GdImageDriver(),
            'imagick' => new Drivers\ImagickImageDriver(),
        };
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
