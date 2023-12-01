<?php

namespace Spatie\Image\Drivers;

use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\BorderType;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Enums\Constraint;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\FlipDirection;
use Spatie\Image\Enums\Orientation;
use Spatie\Image\Size;
use Spatie\ImageOptimizer\OptimizerChain;

interface ImageDriver
{
    public function new(int $width, int $height, string $backgroundColor = null): self;

    public function driverName(): string;

    public function load(string $path): self;

    public function save(string $path = ''): self;

    public function getWidth(): int;

    public function getHeight(): int;

    /**
     * @param  int  $brightness A value between -100 and 100
     */
    public function brightness(int $brightness): self;

    public function gamma(float $gamma): self;

    public function contrast(float $level): self;

    /**
     * @param  int  $blur A value between 0 and 100.
     */
    public function blur(int $blur): self;

    public function colorize(int $red, int $green, int $blue): self;

    public function greyscale(): self;

    public function sepia(): self;

    public function sharpen(float $amount): self;

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

    public function base64(string $imageFormat): string;

    public function background(string $color): self;

    public function overlay(ImageDriver $bottomImage, ImageDriver $topImage, int $x, int $y): self;

    public function orientation(Orientation $orientation = null): self;

    public function exif(): array;

    public function flip(FlipDirection $flip): self;

    public function pixelate(int $pixelate): self;

    public function insert(
        ImageDriver|string $otherImage,
        AlignPosition $position = AlignPosition::Center,
        int $x = 0,
        int $y = 0,
    ): self;

    public function image(): mixed;

    /** @param array<Constraint> $constraints */
    public function resize(int $width, int $height, array $constraints): self;

    /** @param array<Constraint> $constraints */
    public function width(int $width, array $constraints = []): self;

    /** @param array<Constraint> $constraints */
    public function height(int $height, array $constraints = []): self;

    public function border(int $width, BorderType $type, string $color = '000000'): self;

    public function quality(int $quality): self;

    public function format(string $format): self;

    public function optimize(OptimizerChain $optimizerChain): self;
}
