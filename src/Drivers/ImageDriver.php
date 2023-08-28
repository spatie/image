<?php

namespace Spatie\Image\Drivers;

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
}
