<?php

namespace Spatie\Image\Drivers;

use GdImage;
use Intervention\Image\Gd\Color;
use Intervention\Image\Image;
use Spatie\Image\Actions\CalculateFitSizeAction;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Size;

class GdImageDriver implements ImageDriver
{
    use ValidatesArguments;

    private GdImage $image;

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

    public function load(string $path): ImageDriver
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

    public function brightness(int $brightness): ImageDriver
    {
        $this->ensureNumberBetween($brightness, -100, 100, 'brightness');

        // TODO: Convert value between -100 and 100 to -255 and 255
        $brightness = round($brightness * 2.55);

        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $brightness);

        return $this;
    }

    public function blur(int $blur): ImageDriver
    {
        $this->ensureNumberBetween($blur, 0, 100, 'blur');

        for ($i = 0; $i < $blur; $i++) {
            imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
        }

        return $this;
    }

    public function save(string $path): ImageDriver
    {
        // TODO: make this work with other formats.
        imagepng($this->image, $path);

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

    public function fit(Fit $fit, int $desiredWidth = null, int $desiredHeight = null): ImageDriver
    {
        $resize = (new CalculateFitSizeAction())->execute(
            $this->getWidth(),
            $this->getHeight(),
            $fit,
            $desiredWidth,
            $desiredHeight,
        );

        $this->modify($this->getWidth(), $this->getHeight(), $resize->width, $resize->height);

        if ($fit === Fit::Fill) {
            $this->resizeCanvas($desiredWidth, $desiredHeight, AlignPosition::Center);
        }

        return $this;
    }

    protected function modify($originalWidth, $originalHeight, $desiredWidth, $desiredHeight)
    {
        // create new image
        $modified = imagecreatetruecolor(intval($desiredWidth), intval($desiredHeight));

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

        // copy content from resource
        $result = imagecopyresampled(
            $modified,
            $this->image,
            0,
            0,
            0,
            0,
            intval($desiredWidth),
            intval($desiredHeight),
            $originalWidth,
            $originalHeight
        );

        $this->image = $modified;

        return $result;
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
    ): ImageDriver {
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

    public function gamma(float $gamma): ImageDriver
    {
        $this->ensureNumberBetween($gamma, 0.1, 9.99, 'gamma');

        imagegammacorrect($this->image, 1, $gamma);

        return $this;
    }

    public function contrast(float $level): ImageDriver
    {
        $this->ensureNumberBetween($level, -100, 100, 'contrast');

        imagefilter($this->image, IMG_FILTER_CONTRAST, ($level * -1));

        return $this;
    }
}
