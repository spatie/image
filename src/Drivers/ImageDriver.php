<?php

namespace Spatie\Image\Drivers;

use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Size;

interface ImageDriver
{
    public function new(int $width, int $height, string $backgroundColor = null): self;

    public function driverName(): string;

    public function load(string $path): self;

    public function save(string $path): self;

    public function getWidth(): int;

    public function getHeight(): int;

    /**
     * @param  int  $brightness A value between -100 and 100
     */
    public function brightness(int $brightness): self;

    public function gamma(float $gamma): self;

    public function contrast(float $level): self;

    /**
     * @param  int  $amount A value between 0 and 100
     */
    public function blur(int $blur): self;

    public function getSize(): Size;

    public function fit(Fit $fit, int $desiredWidth = null, int $desiredHeight = null): self;

    public function pickColor(int $x, int $y, ColorFormat $colorFormat): mixed;

    public function resizeCanvas(
        int $width = null,
        int $height = null,
        AlignPosition $position = null,
        bool $relative = false,
        string $backgroundColor = '#000000'
    ): self;

    public function manualCrop(int $width, int $height, int $x = 0, int $y = 0): self;

    public function crop(int $width, int $height, CropPosition $position = CropPosition::Center): self;
}
