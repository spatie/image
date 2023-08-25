<?php

namespace Spatie\Image\Drivers;

use Spatie\Image\Exceptions\InvalidManipulation;

interface ImageDriver
{
    public function load(string $path): self;

    public function save(string $path): self;

    public function getWidth(): int;

    public function getHeight(): int;

    /**
     * @param int $brightness A value between -100 and 100
     *
     * @throws InvalidManipulation
     */
    public function brightness(int $brightness): self;
}
