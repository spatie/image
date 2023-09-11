<?php

namespace Spatie\Image\Drivers\Imagick;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use Spatie\Image\Drivers\Concerns\CalculatesCropOffsets;
use Spatie\Image\Drivers\Concerns\CalculatesFocalCropCoordinates;
use Spatie\Image\Drivers\Concerns\GetsOrientationFromExif;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\FlipDirection;
use Spatie\Image\Enums\Orientation;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Point;
use Spatie\Image\Size;

class ImagickDriver implements ImageDriver
{
    use CalculatesCropOffsets;
    use CalculatesFocalCropCoordinates;
    use GetsOrientationFromExif;
    use ValidatesArguments;

    protected Imagick $image;

    protected array $exif = [];

    public function new(int $width, int $height, string $backgroundColor = null): self
    {
        $backgroundColor = new ImagickColor($backgroundColor);

        $image = new Imagick();

        $image->newImage($width, $height, $backgroundColor->getPixel(), 'png');
        $image->setType(Imagick::IMGTYPE_UNDEFINED);
        $image->setImageType(Imagick::IMGTYPE_UNDEFINED);
        $image->setColorspace(Imagick::COLORSPACE_UNDEFINED);

        return (new self())->setImage($image);
    }

    protected function setImage(Imagick $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function load(string $path): self
    {
        $this->image = new Imagick($path);
        $this->exif = $this->image->getImageProperties('exif:*');

        return $this;
    }

    public function image(): Imagick
    {
        return $this->image;
    }

    public function getWidth(): int
    {
        return $this->image->getImageWidth();
    }

    public function getHeight(): int
    {
        return $this->image->getImageHeight();
    }

    public function brightness(int $brightness): self
    {
        $this->ensureNumberBetween($brightness, -100, 100, 'brightness');

        $this->image->modulateImage(100 + $brightness, 100, 100);

        return $this;
    }

    public function blur(int $blur): self
    {
        $this->ensureNumberBetween($blur, 0, 100, 'blur');

        $this->image->blurImage(1 * $blur, 0.5 * $blur);

        return $this;
    }

    public function fit(Fit $fit, int $desiredWidth = null, int $desiredHeight = null): self
    {
        $calculatedSize = $fit->calculateSize(
            $this->getWidth(),
            $this->getHeight(),
            $desiredWidth,
            $desiredHeight
        );

        $this->image->scaleImage($calculatedSize->width, $calculatedSize->height);

        if ($fit->shouldResizeCanvas()) {
            $this->resizeCanvas($desiredWidth, $desiredHeight, AlignPosition::Center);
        }

        return $this;
    }

    public function resizeCanvas(
        int $width = null,
        int $height = null,
        AlignPosition $position = null,
        bool $relative = false,
        string $backgroundColor = '#ffffff'
    ): self {
        $position ??= AlignPosition::Center;

        $originalWidth = $this->getWidth();
        $originalHeight = $this->getHeight();

        $width ??= $originalWidth;
        $height ??= $originalHeight;

        if ($relative) {
            $width = $originalWidth + $width;
            $height = $originalHeight + $height;
        }

        $width = $width <= 0
            ? $width + $originalWidth
            : $width;

        $height = $height <= 0
            ? $height + $originalHeight
            : $height;

        $canvas = $this->new($width, $height, $backgroundColor);

        $canvasSize = $canvas->getSize()->align($position);
        $imageSize = $this->getSize()->align($position);
        $canvasPosition = $imageSize->relativePosition($canvasSize);
        $imagePosition = $canvasSize->relativePosition($imageSize);

        if ($width <= $originalWidth) {
            $destinationX = 0;
            $sourceX = $canvasPosition->x;
            $sourceWidth = $canvasSize->width;
        } else {
            $destinationX = $imagePosition->x;
            $sourceX = 0;
            $sourceWidth = $originalWidth;
        }

        if ($height <= $originalHeight) {
            $destinationY = 0;
            $sourceY = $canvasPosition->y;
            $sourceHeight = $canvasSize->height;
        } else {
            $destinationY = $imagePosition->y;
            $sourceY = 0;
            $sourceHeight = $originalHeight;
        }

        // make image area transparent to keep transparency
        // even if background-color is set
        $rect = new ImagickDraw;
        $fill = $canvas->pickColor(0, 0, ColorFormat::Hex);
        $fill = $fill == '#ff0000' ? '#00ff00' : '#ff0000';
        $rect->setFillColor($fill);
        $rect->rectangle($destinationX, $destinationY, $destinationX + $sourceWidth - 1, $destinationY + $sourceHeight - 1);
        $canvas->image->drawImage($rect);
        $canvas->image->transparentPaintImage($fill, 0, 0, false);

        $canvas->image->setImageColorspace($this->image->getImageColorspace());

        // copy image into new canvas
        $this->image->cropImage($sourceWidth, $sourceHeight, $sourceX, $sourceY);
        $canvas->image->compositeImage($this->image, Imagick::COMPOSITE_DEFAULT, $destinationX, $destinationY);
        $canvas->image->setImagePage(0, 0, 0, 0);

        // set new core to canvas
        $this->image = $canvas->image;

        return $this;
    }

    public function pickColor(int $x, int $y, ColorFormat $colorFormat): mixed
    {
        $color = new ImagickColor($this->image->getImagePixelColor($x, $y));

        return $color->format($colorFormat);
    }

    public function save(string $path): ImageDriver
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if (! in_array(strtoupper($extension), Imagick::queryFormats('*'))) {
            throw UnsupportedImageFormat::make($extension);
        }

        $this->image->writeImage($path);

        return $this;
    }

    public function base64(string $imageFormat = 'jpeg', bool $prefixWithFormat = true): string
    {
        $image = clone $this->image;
        $image->setFormat($imageFormat);

        if ($prefixWithFormat) {
            return 'data:image/'.$imageFormat.';base64,'.base64_encode($image->getImageBlob());
        }

        return base64_encode($image->getImageBlob());
    }

    public function driverName(): string
    {
        return 'imagick';
    }

    public function getSize(): Size
    {
        return new Size($this->getWidth(), $this->getHeight());
    }

    public function gamma(float $gamma): self
    {
        $this->ensureNumberBetween($gamma, 0.1, 9.99, 'gamma');

        $this->image->gammaImage($gamma);

        return $this;
    }

    public function contrast(float $level): self
    {
        $this->ensureNumberBetween($level, -100, 100, 'contrast');

        $this->image->brightnessContrastImage(1, $level);

        return $this;
    }

    public function colorize(int $red, int $green, int $blue): self
    {
        $this->ensureNumberBetween($red, -100, 100, 'red');
        $this->ensureNumberBetween($green, -100, 100, 'green');
        $this->ensureNumberBetween($blue, -100, 100, 'blue');

        $quantumRange = $this->image->getQuantumRange();

        $red = Helpers::normalizeColorizeLevel($red);
        $green = Helpers::normalizeColorizeLevel($green);
        $blue = Helpers::normalizeColorizeLevel($blue);

        $this->image->levelImage(0, $red, $quantumRange['quantumRangeLong'], Imagick::CHANNEL_RED);
        $this->image->levelImage(0, $green, $quantumRange['quantumRangeLong'], Imagick::CHANNEL_GREEN);
        $this->image->levelImage(0, $blue, $quantumRange['quantumRangeLong'], Imagick::CHANNEL_BLUE);

        return $this;
    }

    public function greyscale(): self
    {
        $this->image->modulateImage(100, 0, 100);

        return $this;
    }

    public function manualCrop(int $width, int $height, int $x = null, int $y = null): self
    {
        $cropped = new Size($width, $height);
        $position = new Point($x ?? 0, $y ?? 0);

        if (is_null($x) && is_null($y)) {
            $position = $this
                ->getSize()
                ->align(AlignPosition::Center)
                ->relativePosition($cropped->align(AlignPosition::Center));
        }

        $this->image->cropImage($cropped->width, $cropped->height, $position->x, $position->y);
        $this->image->setImagePage(0, 0, 0, 0);

        return $this;
    }

    public function crop(int $width, int $height, CropPosition $position = CropPosition::Center): self
    {
        [$offsetX, $offsetY] = $this->calculateCropOffsets($width, $height, $position);

        return $this->manualCrop($width, $height, $offsetX, $offsetY);
    }

    public function focalCrop(int $width, int $height, $cropCenterX = null, $cropCenterY = null): self
    {
        [$width, $height, $cropCenterX, $cropCenterY] = $this->calculateFocalCropCoordinates(
            $width,
            $height,
            $cropCenterX,
            $cropCenterY
        );

        $this->manualCrop($width, $height, $cropCenterX, $cropCenterY);

        return $this;
    }

    public function sepia(): ImageDriver
    {
        return $this
            ->greyscale()
            ->brightness(-40)
            ->contrast(20)
            ->colorize(50, 35, 20)
            ->brightness(-10)
            ->contrast(10);
    }

    public function sharpen(float $amount): self
    {
        $this->ensureNumberBetween($amount, 0, 100, 'sharpen');

        $this->image->unsharpMaskImage(1, 1, $amount / 6.25, 0);

        return $this;
    }

    public function background(string $color): self
    {
        $background = $this->new($this->getWidth(), $this->getHeight(), $color);

        $this->overlay($background, $this, 0, 0);

        return $this;
    }

    public function overlay(ImageDriver $bottomImage, ImageDriver $topImage, int $x, int $y): self
    {
        $bottomImage->image()->compositeImage($this->image, Imagick::COMPOSITE_DEFAULT, $x, $y);
        $this->image = $bottomImage->image();

        return $this;
    }

    public function orientation(Orientation $orientation = null): self
    {
        if (is_null($orientation)) {
            $orientation = $this->getOrientationFromExif($this->exif);
        }

        $this->image->rotateImage(new ImagickPixel('none'), $orientation->degrees());

        return $this;
    }

    public function exif(): array
    {
        return $this->exif;
    }

    public function flip(FlipDirection $flip): self
    {
        switch ($flip) {
            case FlipDirection::Vertical:
                $this->image->flipImage();
                break;
            case FlipDirection::Horizontal:
                $this->image->flopImage();
                break;
            case FlipDirection::Both:
                $this->image->flipImage();
                $this->image->flopImage();
                break;
        }

        return $this;
    }
}
