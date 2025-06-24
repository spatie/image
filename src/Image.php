<?php

namespace Spatie\Image;

use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Drivers\Gd\GdDriver;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Drivers\Imagick\ImagickDriver;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\BorderType;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Enums\Constraint;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\FlipDirection;
use Spatie\Image\Enums\ImageDriver as ImageDriverEnum;
use Spatie\Image\Enums\Orientation;
use Spatie\Image\Enums\Unit;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\InvalidImageDriver;
use Spatie\ImageOptimizer\OptimizerChain;

class Image implements ImageDriver
{
    use ValidatesArguments;

    protected ImageDriver $imageDriver;

    public function __construct(?string $pathToImage = null)
    {
        $this->imageDriver = new ImagickDriver;

        if ($pathToImage) {
            $this->imageDriver->loadFile($pathToImage);
        }
    }

    public static function load(string $pathToImage): static
    {
        if (! file_exists($pathToImage)) {
            throw CouldNotLoadImage::fileDoesNotExist($pathToImage);
        }

        return new static($pathToImage);
    }

    public function loadFile(string $pathToImage): static
    {
        $this->imageDriver->loadFile($pathToImage);

        return $this;
    }

    public static function useImageDriver(ImageDriverEnum|string $imageDriver): static
    {
        $image = new static;

        if (is_subclass_of($imageDriver, ImageDriver::class)) {
            /** @var ImageDriver $imageDriver */
            $image->imageDriver = new $imageDriver;

            return $image;
        }

        if (is_string($imageDriver)) {
            $imageDriver = ImageDriverEnum::tryFrom($imageDriver)
                ?? throw InvalidImageDriver::driver($imageDriver);
        }

        $image->imageDriver = match ($imageDriver) {
            ImageDriverEnum::Gd => new GdDriver,
            ImageDriverEnum::Imagick => new ImagickDriver,
        };

        return $image;
    }

    public function new(int $width, int $height, ?string $backgroundColor = null): static
    {
        $this->imageDriver->new($width, $height, $backgroundColor);

        return $this;
    }

    public function driverName(): string
    {
        return $this->imageDriver->driverName();
    }

    public function save(string $path = ''): static
    {
        $this->imageDriver->save($path);

        return $this;
    }

    public function getWidth(): int
    {
        return $this->imageDriver->getWidth();
    }

    public function getHeight(): int
    {
        return $this->imageDriver->getHeight();
    }

    public function brightness(int $brightness): static
    {
        $this->ensureNumberBetween($brightness, -100, 100, 'brightness');

        $this->imageDriver->brightness($brightness);

        return $this;
    }

    public function gamma(float $gamma): static
    {
        $this->ensureNumberBetween($gamma, 0.1, 9.99, 'gamma');

        $this->imageDriver->gamma($gamma);

        return $this;
    }

    public function contrast(float $level): static
    {
        $this->ensureNumberBetween($level, -100, 100, 'contrast');

        $this->imageDriver->contrast($level);

        return $this;
    }

    public function blur(int $blur): static
    {
        $this->ensureNumberBetween($blur, 0, 100, 'blur');

        $this->imageDriver->blur($blur);

        return $this;
    }

    public function colorize(int $red, int $green, int $blue): static
    {
        $this->ensureNumberBetween($red, -100, 100, 'red');
        $this->ensureNumberBetween($green, -100, 100, 'green');
        $this->ensureNumberBetween($blue, -100, 100, 'blue');

        $this->imageDriver->colorize($red, $green, $blue);

        return $this;
    }

    public function greyscale(): static
    {
        $this->imageDriver->greyscale();

        return $this;
    }

    public function sepia(): static
    {
        $this->imageDriver->sepia();

        return $this;
    }

    public function sharpen(float $amount): static
    {
        $this->ensureNumberBetween($amount, 0, 100, 'sharpen');

        $this->imageDriver->sharpen($amount);

        return $this;
    }

    public function getSize(): Size
    {
        return $this->imageDriver->getSize();
    }

    public function fit(
        Fit $fit,
        ?int $desiredWidth = null,
        ?int $desiredHeight = null,
        bool $relative = false,
        string $backgroundColor = '#ffffff'
    ): static {
        $this->imageDriver->fit($fit, $desiredWidth, $desiredHeight, $relative, $backgroundColor);

        return $this;
    }

    public function pickColor(int $x, int $y, ColorFormat $colorFormat): mixed
    {
        return $this->imageDriver->pickColor($x, $y, $colorFormat);
    }

    public function resizeCanvas(?int $width = null, ?int $height = null, ?AlignPosition $position = null, bool $relative = false, string $backgroundColor = '#000000'): static
    {
        $this->imageDriver->resizeCanvas($width, $height, $position, $relative, $backgroundColor);

        return $this;
    }

    public function manualCrop(int $width, int $height, ?int $x = null, ?int $y = null): static
    {
        $this->imageDriver->manualCrop($width, $height, $x, $y);

        return $this;
    }

    public function crop(int $width, int $height, CropPosition $position = CropPosition::Center): static
    {
        $this->imageDriver->crop($width, $height, $position);

        return $this;
    }

    public function focalCrop(int $width, int $height, ?int $cropCenterX = null, ?int $cropCenterY = null): static
    {
        $this->imageDriver->focalCrop($width, $height, $cropCenterX, $cropCenterY);

        return $this;
    }

    public function focalCropAndResize(int $width, int $height, ?int $cropCenterX = null, ?int $cropCenterY = null): static
    {
        $this->imageDriver->focalCropAndResize($width, $height, $cropCenterX, $cropCenterY);

        return $this;
    }

    public function base64(string $imageFormat = 'jpeg', bool $prefixWithFormat = true): string
    {
        return $this->imageDriver->base64($imageFormat, $prefixWithFormat);
    }

    public function background(string $color): static
    {
        $this->imageDriver->background($color);

        return $this;
    }

    public function overlay(ImageDriver $bottomImage, ImageDriver $topImage, int $x, int $y): static
    {
        $this->imageDriver->overlay($bottomImage, $topImage, $x, $y);

        return $this;
    }

    public function orientation(?Orientation $orientation = null): static
    {
        $this->imageDriver->orientation($orientation);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function exif(): array
    {
        return $this->imageDriver->exif();
    }

    public function flip(FlipDirection $flip): static
    {
        $this->imageDriver->flip($flip);

        return $this;
    }

    public function pixelate(int $pixelate = 50): static
    {
        $this->ensureNumberBetween($pixelate, 0, 100, 'pixelate');

        $this->imageDriver->pixelate($pixelate);

        return $this;
    }

    public function insert(ImageDriver|string $otherImage, AlignPosition $position = AlignPosition::Center, int $x = 0, int $y = 0, int $alpha = 100): static
    {
        $this->imageDriver->insert($otherImage, $position, $x, $y, $alpha);

        return $this;
    }

    public function image(): mixed
    {
        return $this->imageDriver->image();
    }

    public function resize(int $width, int $height, array $constraints = []): static
    {
        $this->imageDriver->resize($width, $height, $constraints);

        return $this;
    }

    public function width(int $width, array $constraints = [Constraint::PreserveAspectRatio]): static
    {
        $this->imageDriver->width($width, $constraints);

        return $this;
    }

    public function height(int $height, array $constraints = [Constraint::PreserveAspectRatio]): static
    {
        $this->imageDriver->height($height, $constraints);

        return $this;
    }

    public function border(int $width, BorderType $type, string $color = '000000'): static
    {
        $this->imageDriver->border($width, $type, $color);

        return $this;
    }

    public function quality(int $quality): static
    {
        $this->ensureNumberBetween($quality, 0, 100, 'quality');

        $this->imageDriver->quality($quality);

        return $this;
    }

    public function format(string $format): static
    {
        $this->imageDriver->format($format);

        return $this;
    }

    public function optimize(?OptimizerChain $optimizerChain = null): static
    {
        $this->imageDriver->optimize($optimizerChain);

        return $this;
    }

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
    ): static {
        $this->imageDriver->watermark(
            $watermarkImage,
            $position,
            $paddingX,
            $paddingY,
            $paddingUnit,
            $width,
            $widthUnit,
            $height,
            $heightUnit,
            $fit,
            $alpha,
        );

        return $this;
    }

    public function text(
        string $text,
        int $fontSize,
        string $color = '000000',
        int $x = 0,
        int $y = 0,
        int $angle = 0,
        string $fontPath = '',
        int $width = 0,
    ): static {
        $this->imageDriver->text(
            $text,
            $fontSize,
            $color,
            $x,
            $y,
            $angle,
            $fontPath,
            $width,
        );

        return $this;
    }

    public function wrapText(string $text, int $fontSize, string $fontPath = '', int $angle = 0, int $width = 0): string
    {
        return $this->imageDriver->wrapText(
            $text,
            $fontSize,
            $fontPath,
            $angle,
            $width,
        );
    }
}
