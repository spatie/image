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
use Spatie\Image\Enums\Unit;
use Spatie\Image\Size;
use Spatie\ImageOptimizer\OptimizerChain;

interface ImageDriver
{
    public function new(int $width, int $height, ?string $backgroundColor = null): static;

    public function loadFile(string $path): static;

    public function driverName(): string;

    public function save(string $path = ''): static;

    public function getWidth(): int;

    public function getHeight(): int;

    /**
     * @param  int  $brightness  A value between -100 and 100
     */
    public function brightness(int $brightness): static;

    public function gamma(float $gamma): static;

    public function contrast(float $level): static;

    /**
     * @param  int  $blur  A value between 0 and 100.
     */
    public function blur(int $blur): static;

    public function colorize(int $red, int $green, int $blue): static;

    public function greyscale(): static;

    public function sepia(): static;

    public function sharpen(float $amount): static;

    public function getSize(): Size;

    public function fit(
        Fit $fit,
        ?int $desiredWidth = null,
        ?int $desiredHeight = null,
        bool $relative = false,
        string $backgroundColor = '#ffffff'
    ): static;

    public function pickColor(int $x, int $y, ColorFormat $colorFormat): mixed;

    public function resizeCanvas(
        ?int $width = null,
        ?int $height = null,
        ?AlignPosition $position = null,
        bool $relative = false,
        string $backgroundColor = '#000000'
    ): static;

    public function manualCrop(int $width, int $height, int $x = 0, int $y = 0): static;

    public function crop(int $width, int $height, CropPosition $position = CropPosition::Center): static;

    public function focalCrop(int $width, int $height, ?int $cropCenterX = null, ?int $cropCenterY = null): static;

    public function focalCropAndResize(int $width, int $height, ?int $cropCenterX = null, ?int $cropCenterY = null): static;

    public function base64(string $imageFormat, bool $prefixWithFormat = true): string;

    public function background(string $color): static;

    public function overlay(ImageDriver $bottomImage, ImageDriver $topImage, int $x, int $y): static;

    public function orientation(?Orientation $orientation = null): static;

    /**
     * @return array<string, mixed>
     */
    public function exif(): array;

    public function flip(FlipDirection $flip): static;

    public function pixelate(int $pixelate): static;

    public function watermark(
        ImageDriver|string $watermarkImage,
        AlignPosition $position = AlignPosition::BottomRight,
        int $paddingX = 0,
        int $paddingY = 0,
        Unit $paddingUnit = Unit::Pixel,
        int $width = 0,
        Unit $widthUnit = Unit::Pixel,
        int $height = 0,
        Unit $heightUnit = Unit::Pixel,
        Fit $fit = Fit::Contain,
        int $alpha = 100
    ): static;

    public function insert(
        ImageDriver|string $otherImage,
        AlignPosition $position = AlignPosition::Center,
        int $x = 0,
        int $y = 0,
        int $alpha = 100
    ): static;

    public function text(
        string $text,
        int $fontSize,
        string $color = '000000',
        int $x = 0,
        int $y = 0,
        int $angle = 0,
        string $fontPath = '',
        int $width = 0,
    ): static;

    public function wrapText(
        string $text,
        int $fontSize,
        string $fontPath = '',
        int $angle = 0,
        int $width = 0,
    ): string;

    public function image(): mixed;

    /** @param  array<Constraint>  $constraints */
    public function resize(int $width, int $height, array $constraints): static;

    /** @param  array<Constraint>  $constraints */
    public function width(int $width, array $constraints = []): static;

    /** @param  array<Constraint>  $constraints */
    public function height(int $height, array $constraints = []): static;

    public function border(int $width, BorderType $type, string $color = '000000'): static;

    public function quality(int $quality): static;

    public function format(string $format): static;

    public function optimize(?OptimizerChain $optimizerChain = null): static;
}
