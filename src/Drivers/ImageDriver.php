<?php

namespace Spatie\Image\Drivers;

interface ImageDriver
{
    public function load(string $path): self;

    public function save(string $path): self;

    public function getWidth(): int;

    public function getHeight(): int;

    public function brightness(int $brightness): self;
}
