<?php

namespace Spatie\Image\Drivers;

interface ImageDriver
{
    public static function load(string $path): self;

    public function getWidth(): int;

    public function getHeight(): int;

    public function brightness(int $brightness): self;
}
