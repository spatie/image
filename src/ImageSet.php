<?php

namespace Spatie\Image;

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\ImageMethodDoesNotExist;

///** @mixin ImageDriver */
class ImageSet
{
    public function __construct(protected array $images)
    {
    }

    public static function load(array $paths): self
    {
        $images = [];

        foreach ($paths as $path) {
            $images[] = Image::load($path);
        }

        return (new self($images));
    }

    public function save(array $paths): self
    {
        $images = [];

        foreach ($this->images as $i => $image) {
            $image->save($paths[$i]);
        }

        return (new self($images));
    }

    public function __call(string $methodName, array $arguments): ImageSet
    {
        foreach ($this->images as $image) {
            if (! method_exists($image, $methodName)) {
                throw ImageMethodDoesNotExist::make($methodName);
            }

            $image->$methodName(...$arguments);
        }

        return $this;
    }
}
