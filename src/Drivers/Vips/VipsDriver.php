<?php

namespace Spatie\Image\Drivers\Vips;

use Jcupitt\Vips\Image;
use Spatie\Image\Drivers\Concerns\AddsWatermark;
use Spatie\Image\Drivers\Concerns\CalculatesCropOffsets;
use Spatie\Image\Drivers\Concerns\CalculatesFocalCropCoordinates;
use Spatie\Image\Drivers\Concerns\GetsOrientationFromExif;
use Spatie\Image\Drivers\Concerns\PerformsFitCrops;
use Spatie\Image\Drivers\Concerns\PerformsOptimizations;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\BorderType;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\FlipDirection;
use Spatie\Image\Enums\Orientation;
use Spatie\Image\Enums\Unit;
use Spatie\Image\Size;
use Spatie\ImageOptimizer\OptimizerChain;

class VipsDriver implements ImageDriver
{
    use AddsWatermark;
    use CalculatesCropOffsets;
    use CalculatesFocalCropCoordinates;
    use GetsOrientationFromExif;
    use PerformsFitCrops;
    use PerformsOptimizations;
    use ValidatesArguments;

    protected Image $image;

    public function new(int $width, int $height, ?string $backgroundColor = null): static
    {
        // TODO: Implement new() method.
    }

    public function loadFile(string $path): static
    {
        $this->image = Image::newFromFile($path);

        return $this;
    }

    public function driverName(): string
    {
        return 'vips';

    }

    public function save(string $path = ''): static
    {
        $this->image->writeToFile($path);

        return $this;
    }

    public function getWidth(): int
    {
        return $this->image->width;
    }

    public function getHeight(): int
    {
        return $this->image->height;
    }

    public function brightness(int $brightness): static
    {
        $this->image = $this->image->add($brightness);

        return $this;
    }

    public function gamma(float $gamma): static
    {
        $this->image = $this->image->gamma(['exponent' => $gamma]);

        return $this;
    }

    public function contrast(float $level): static
    {

    }

    public function blur(int $blur): static
    {

    }

    public function colorize(int $red, int $green, int $blue): static
    {
        // TODO: Implement colorize() method.
    }

    public function greyscale(): static
    {
        // TODO: Implement greyscale() method.
    }

    public function sepia(): static
    {
        // TODO: Implement sepia() method.
    }

    public function sharpen(float $amount): static
    {
        // TODO: Implement sharpen() method.
    }

    public function getSize(): Size
    {
        return new Size($this->getWidth(), $this->getHeight());
    }

    public function fit(Fit $fit, ?int $desiredWidth = null, ?int $desiredHeight = null, bool $relative = false, string $backgroundColor = '#ffffff'): static
    {
        // TODO: Implement fit() method.
    }

    public function pickColor(int $x, int $y, ColorFormat $colorFormat): mixed
    {
        // TODO: Implement pickColor() method.
    }

    public function resizeCanvas(?int $width = null, ?int $height = null, ?AlignPosition $position = null, bool $relative = false, string $backgroundColor = '#000000'): static
    {
        // TODO: Implement resizeCanvas() method.
    }

    public function manualCrop(int $width, int $height, int $x = 0, int $y = 0): static
    {
        // TODO: Implement manualCrop() method.
    }

    public function crop(int $width, int $height, CropPosition $position = CropPosition::Center): static
    {
        // TODO: Implement crop() method.
    }

    public function focalCrop(int $width, int $height, ?int $cropCenterX = null, ?int $cropCenterY = null): static
    {
        // TODO: Implement focalCrop() method.
    }

    public function base64(string $imageFormat, bool $prefixWithFormat = true): string
    {
        // TODO: Implement base64() method.
    }

    public function background(string $color): static
    {
        // TODO: Implement background() method.
    }

    public function overlay(ImageDriver $bottomImage, ImageDriver $topImage, int $x, int $y): static
    {
        // TODO: Implement overlay() method.
    }

    public function orientation(?Orientation $orientation = null): static
    {
        // TODO: Implement orientation() method.
    }

    public function exif(): array
    {
        // TODO: Implement exif() method.
    }

    public function flip(FlipDirection $flip): static
    {
        // TODO: Implement flip() method.
    }

    public function pixelate(int $pixelate): static
    {
        // TODO: Implement pixelate() method.
    }

    public function watermark(ImageDriver|string $watermarkImage, AlignPosition $position = AlignPosition::BottomRight, int $paddingX = 0, int $paddingY = 0, Unit $paddingUnit = Unit::Pixel, int $width = 0, Unit $widthUnit = Unit::Pixel, int $height = 0, Unit $heightUnit = Unit::Pixel, Fit $fit = Fit::Contain, int $alpha = 100): static
    {
        // TODO: Implement watermark() method.
    }

    public function insert(ImageDriver|string $otherImage, AlignPosition $position = AlignPosition::Center, int $x = 0, int $y = 0, int $alpha = 100): static
    {
        // TODO: Implement insert() method.
    }

    public function text(string $text, int $fontSize, string $color = '000000', int $x = 0, int $y = 0, int $angle = 0, string $fontPath = '', int $width = 0,): static
    {
        // TODO: Implement text() method.
    }

    public function wrapText(string $text, int $fontSize, string $fontPath = '', int $angle = 0, int $width = 0,): string
    {
        // TODO: Implement wrapText() method.
    }

    public function image(): mixed
    {
        // TODO: Implement image() method.
    }

    public function resize(int $width, int $height, array $constraints): static
    {


        $this->image->scale();
    }

    public function width(int $width, array $constraints = []): static
    {
        $newHeight = (int) round($width / $this->getSize()->aspectRatio());

        $this->image = $this->image->thumbnail_image($width, [
            'height' => $newHeight,
            'crop' => 'centre',
        ]);

        return $this;
    }

    public function height(int $height, array $constraints = []): static
    {
        // TODO: Implement height() method.
    }

    public function border(int $width, BorderType $type, string $color = '000000'): static
    {
        // TODO: Implement border() method.
    }

    public function quality(int $quality): static
    {
        // TODO: Implement quality() method.
    }

    public function format(string $format): static
    {
        // TODO: Implement format() method.
    }

    public function optimize(?OptimizerChain $optimizerChain = null): static
    {
        // TODO: Implement optimize() method.
    }
}
