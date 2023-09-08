<?php

namespace Spatie\Image\Drivers\Gd;

use GdImage;
use Spatie\Image\Drivers\Concerns\CalculatesCropOffsets;
use Spatie\Image\Drivers\Concerns\CalculatesFocalCropCoordinates;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Point;
use Spatie\Image\Size;

class GdDriver implements ImageDriver
{
    use CalculatesCropOffsets;
    use CalculatesFocalCropCoordinates;
    use ValidatesArguments;

    protected GdImage $image;

    public function new(int $width, int $height, string $backgroundColor = null): self
    {
        $image = imagecreatetruecolor($width, $height);

        $backgroundColor = new GdColor($backgroundColor);

        imagefill($image, 0, 0, $backgroundColor->getInt());

        return (new self)->setImage($image);
    }

    protected function setImage(GdImage $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function load(string $path): self
    {
        $handle = fopen($path, 'r');

        $contents = fread($handle, filesize($path));

        fclose($handle);

        $image = imagecreatefromstring($contents);

        if (! $image) {
            throw CouldNotLoadImage::make($path);
        }

        $this->image = $image;

        return $this;
    }

    public function getWidth(): int
    {
        return imagesx($this->image);
    }

    public function getHeight(): int
    {
        return imagesy($this->image);
    }

    public function brightness(int $brightness): self
    {
        $this->ensureNumberBetween($brightness, -100, 100, 'brightness');

        // TODO: Convert value between -100 and 100 to -255 and 255
        $brightness = round($brightness * 2.55);

        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $brightness);

        return $this;
    }

    public function blur(int $blur): self
    {
        $this->ensureNumberBetween($blur, 0, 100, 'blur');

        for ($i = 0; $i < $blur; $i++) {
            imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
        }

        return $this;
    }

    public function save(string $path): self
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($this->image, $path);
                break;
            case 'png':
                imagepng($this->image, $path);
                break;
            case 'gif':
                imagegif($this->image, $path);
                break;
            case 'webp':
                imagewebp($this->image, $path);
                break;
            default:
                throw UnsupportedImageFormat::make($extension);
        }

        return $this;
    }

    public function driverName(): string
    {
        return 'gd';
    }

    public function getSize(): Size
    {
        return new Size($this->getWidth(), $this->getHeight());
    }

    public function fit(Fit $fit, int $desiredWidth = null, int $desiredHeight = null): self
    {
        $calculatedSize = $fit->calculateSize(
            $this->getWidth(),
            $this->getHeight(),
            $desiredWidth,
            $desiredHeight
        );

        $this->modify($this->getWidth(), $this->getHeight(), $calculatedSize->width, $calculatedSize->height);

        if ($fit->shouldResizeCanvas()) {
            $this->resizeCanvas($desiredWidth, $desiredHeight, AlignPosition::Center);
        }

        return $this;
    }

    protected function modify(
        int $originalWidth,
        int $originalHeight,
        int $desiredWidth,
        int $desiredHeight,
        int $sourceX = 0,
        int $sourceY = 0,
    ): self {
        // create new image
        $modified = imagecreatetruecolor($desiredWidth, $desiredHeight);

        // preserve transparency
        $transIndex = imagecolortransparent($this->image);

        if ($transIndex != -1) {
            $rgba = imagecolorsforindex($modified, $transIndex);
            $transColor = imagecolorallocatealpha($modified, $rgba['red'], $rgba['green'], $rgba['blue'], 127);
            imagefill($modified, 0, 0, $transColor);
            imagecolortransparent($modified, $transColor);
        } else {
            imagealphablending($modified, false);
            imagesavealpha($modified, true);
        }

        imagecopyresampled(
            $modified,
            $this->image,
            0,
            0,
            $sourceX,
            $sourceY,
            $desiredWidth,
            $desiredHeight,
            $desiredWidth,
            $desiredHeight,
        );

        $this->image = $modified;

        return $this;
    }

    public function pickColor(int $x, int $y, ColorFormat $colorFormat): mixed
    {
        $color = imagecolorat($this->image, $x, $y);

        if (! imageistruecolor($this->image)) {
            $color = imagecolorsforindex($this->image, $color);
            $color['alpha'] = round(1 - $color['alpha'] / 127, 2);
        }

        $color = new GdColor($color);

        return $color->format($colorFormat);
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

        // check for negative width/height
        $width = ($width <= 0) ? $width + $originalWidth : $width;
        $height = ($height <= 0) ? $height + $originalHeight : $height;

        // create new canvas
        $canvas = $this->new($width, $height, $backgroundColor);

        // set copy position
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
        $transparent = imagecolorallocatealpha($canvas->image, 255, 255, 255, 127);
        imagealphablending($canvas->image, false); // do not blend / just overwrite
        imagefilledrectangle($canvas->image, $destinationX, $destinationY, $destinationX + $sourceWidth - 1, $destinationY + $sourceHeight - 1, $transparent);

        // copy image into new canvas
        imagecopy($canvas->image, $this->image, $destinationX, $destinationY, $sourceX, $sourceY, $sourceWidth, $sourceHeight);

        // set new core to canvas
        $this->image = $canvas->image;

        return $this;
    }

    public function gamma(float $gamma): self
    {
        $this->ensureNumberBetween($gamma, 0.1, 9.99, 'gamma');

        imagegammacorrect($this->image, 1, $gamma);

        return $this;
    }

    public function contrast(float $level): self
    {
        $this->ensureNumberBetween($level, -100, 100, 'contrast');

        imagefilter($this->image, IMG_FILTER_CONTRAST, ($level * -1));

        return $this;
    }

    public function colorize(int $red, int $green, int $blue): self
    {
        $this->ensureNumberBetween($red, -100, 100, 'red');
        $this->ensureNumberBetween($green, -100, 100, 'green');
        $this->ensureNumberBetween($blue, -100, 100, 'blue');

        $red = round($red * 2.55);
        $green = round($green * 2.55);
        $blue = round($blue * 2.55);

        imagefilter($this->image, IMG_FILTER_COLORIZE, $red, $green, $blue);

        return $this;
    }

    public function greyscale(): self
    {
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);

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

        $this->modify(
            $position->x,
            $position->y,
            $cropped->width,
            $cropped->height,
            $position->x,
            $position->y,
        );

        return $this;
    }

    public function crop(int $width, int $height, CropPosition $position = CropPosition::Center): self
    {
        $width = min($width, $this->getWidth());
        $height = min($height, $this->getHeight());

        [$offsetX, $offsetY] = $this->calculateCropOffsets($width, $height, $position);

        $maxWidth = $this->getWidth() - $offsetX;
        $maxHeight = $this->getHeight() - $offsetY;
        $width = min($width, $maxWidth);
        $height = min($height, $maxHeight);

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
            ->brightness(0)
            ->contrast(5)
            ->colorize(38, 25, 10)
            ->contrast(5);
    }
}
