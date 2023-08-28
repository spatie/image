<?php

namespace Spatie\Image\Drivers;

use Spatie\Image\Size;

interface ImageDriver
{
    public function driverName(): string;

    public function load(string $path): self;

    public function save(string $path): self;

    public function getWidth(): int;

    public function getHeight(): int;

    /**
     * @param  int  $brightness A value between -100 and 100
     */
    public function brightness(int $brightness): self;

    /**
     * @param  int  $amount A value between 0 and 100
     */
    public function blur(int $blur): self;

    public function getSize(): Size;
}
