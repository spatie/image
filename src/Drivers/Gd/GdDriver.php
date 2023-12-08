<?php

namespace Spatie\Image\Drivers\Gd;

use GdImage;
use Spatie\Image\Drivers\Concerns\CalculatesCropOffsets;
use Spatie\Image\Drivers\Concerns\CalculatesFocalCropCoordinates;
use Spatie\Image\Drivers\Concerns\GetsOrientationFromExif;
use Spatie\Image\Drivers\Concerns\PerformsOptimizations;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\BorderType;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Enums\Constraint;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\FlipDirection;
use Spatie\Image\Enums\Orientation;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Point;
use Spatie\Image\Size;

class GdDriver implements ImageDriver
{
    use CalculatesCropOffsets;
    use CalculatesFocalCropCoordinates;
    use GetsOrientationFromExif;
    use PerformsOptimizations;
    use ValidatesArguments;

    protected GdImage $image;

    protected array $exif = [];

    protected int $quality = -1;

    protected string $originalPath;

    public function new(int $width, int $height, ?string $backgroundColor = null): self
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
        $this->optimize = false;
        $this->quality = -1;
        $this->originalPath = $path;

        $this->setExif($path);

        $handle = fopen($path, 'r');

        $contents = '';
        if (filesize($path)) {
            $contents = fread($handle, filesize($path));
        }

        fclose($handle);

        $image = imagecreatefromstring($contents);

        if (! $image) {
            throw CouldNotLoadImage::make($path);
        }

        $this->image = $image;

        return $this;
    }

    public function image(): GdImage
    {
        return $this->image;
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

    public function save(?string $path = null): self
    {
        if (! $path) {
            $path = $this->originalPath;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($this->image, $path, $this->quality);
                break;
            case 'png':
                imagepng($this->image, $path, $this->pngCompression());
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

        if ($this->optimize) {
            $this->optimizerChain->optimize($path);
        }

        return $this;
    }

    public function base64(string $imageFormat = 'jpeg', bool $prefixWithFormat = true): string
    {
        ob_start();

        $this->format($imageFormat);

        $image_data = ob_get_contents();
        ob_end_clean();

        if ($prefixWithFormat) {
            return 'data:image/'.$imageFormat.';base64,'.base64_encode($image_data);
        }

        return base64_encode($image_data);
    }

    public function driverName(): string
    {
        return 'gd';
    }

    public function getSize(): Size
    {
        return new Size($this->getWidth(), $this->getHeight());
    }

    public function fit(Fit $fit, ?int $desiredWidth = null, ?int $desiredHeight = null): self
    {
        $calculatedSize = $fit->calculateSize(
            $this->getWidth(),
            $this->getHeight(),
            $desiredWidth,
            $desiredHeight
        );

        $this->modify(
            $calculatedSize->width,
            $calculatedSize->height,
            0,
            0,
            $this->getWidth(),
            $this->getHeight(),
        );

        if ($fit->shouldResizeCanvas()) {
            $this->resizeCanvas($desiredWidth, $desiredHeight, AlignPosition::Center);
        }

        return $this;
    }

    protected function modify(
        int $desiredWidth,
        int $desiredHeight,
        int $sourceX = 0,
        int $sourceY = 0,
        int $sourceWidth = 0,
        int $sourceHeight = 0,
    ): self {
        $newImage = imagecreatetruecolor($desiredWidth, $desiredHeight);

        $transparentColorValue = imagecolortransparent($this->image);

        if ($transparentColorValue !== -1) {
            $rgba = imagecolorsforindex($newImage, $transparentColorValue);

            $transparentColor = imagecolorallocatealpha(
                $newImage,
                $rgba['red'],
                $rgba['green'],
                $rgba['blue'],
                127
            );
            imagefill($newImage, 0, 0, $transparentColor);
            imagecolortransparent($newImage, $transparentColor);
        } else {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        imagecopyresampled(
            $newImage,
            $this->image,
            0,
            0,
            $sourceX,
            $sourceY,
            $desiredWidth,
            $desiredHeight,
            $sourceWidth,
            $sourceHeight,
        );

        $this->image = $newImage;

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
        ?int $width = null,
        ?int $height = null,
        ?AlignPosition $position = null,
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

    public function manualCrop(int $width, int $height, ?int $x = null, ?int $y = null): self
    {
        $cropped = new Size($width, $height);
        $position = new Point($x ?? 0, $y ?? 0);

        if (is_null($x) && is_null($y)) {
            $position = $this
                ->getSize()
                ->align(AlignPosition::Center)
                ->relativePosition($cropped->align(AlignPosition::Center));
        }

        $maxCroppedWidth = $this->getWidth() - $x;
        $maxCroppedHeight = $this->getHeight() - $y;

        $width = min($cropped->width, $maxCroppedWidth);
        $height = min($cropped->height, $maxCroppedHeight);

        $this->modify(
            $width,
            $height,
            $position->x,
            $position->y,
            $width,
            $height,
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

    public function focalCrop(int $width, int $height, ?int $cropCenterX = null, ?int $cropCenterY = null): self
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

    public function sharpen(float $amount): self
    {
        $this->ensureNumberBetween($amount, 0, 100, 'sharpen');

        $min = $amount >= 10 ? $amount * -0.01 : 0;
        $max = $amount * -0.025;
        $abs = ((4 * $min + 4 * $max) * -1) + 1;

        $matrix = [
            [$min, $max, $min],
            [$max, $abs, $max],
            [$min, $max, $min],
        ];

        imageconvolution($this->image, $matrix, 1, 0);

        return $this;
    }

    public function background(string $color): self
    {
        $width = $this->getWidth();
        $height = $this->getHeight();

        $newImage = $this->new($width, $height, $color);

        $backgroundSize = $newImage->getSize()->align(AlignPosition::TopLeft);
        $overlaySize = $this->getSize()->align(AlignPosition::TopLeft);
        $target = $backgroundSize->relativePosition($overlaySize);

        $this->overlay($newImage, $this, $target->x, $target->y);

        return $this;
    }

    public function overlay(ImageDriver $bottomImage, ImageDriver $topImage, int $x = 0, int $y = 0): self
    {
        $bottomImage->insert($topImage, AlignPosition::TopLeft, $x, $y);
        $this->image = $bottomImage->image();

        return $this;
    }

    public function orientation(?Orientation $orientation = null): self
    {
        if (is_null($orientation)) {
            $orientation = $this->getOrientationFromExif($this->exif);
        }

        $this->image = imagerotate($this->image, $orientation->degrees() * -1, 0);

        return $this;
    }

    public function setExif(string $path): void
    {
        /*
        $result = exif_read_data($path);

        if (! is_array($result)) {
            $this->exif = [];

            return;
        }

        $this->exif = $result;
        */
    }

    public function exif(): array
    {
        return $this->exif;
    }

    public function flip(FlipDirection $flip): self
    {
        $direction = match ($flip) {
            FlipDirection::Horizontal => IMG_FLIP_HORIZONTAL,
            FlipDirection::Vertical => IMG_FLIP_VERTICAL,
            FlipDirection::Both => IMG_FLIP_BOTH,
        };

        imageflip($this->image, $direction);

        return $this;
    }

    public function pixelate(int $pixelate = 50): self
    {
        $this->ensureNumberBetween($pixelate, 0, 100, 'pixelate');

        imagefilter($this->image, IMG_FILTER_PIXELATE, $pixelate, true);

        return $this;
    }

    public function insert(
        ImageDriver|string $otherImage,
        AlignPosition $position = AlignPosition::Center,
        int $x = 0,
        int $y = 0,
    ): self {
        if (is_string($otherImage)) {
            $otherImage = (new self())->load($otherImage);
        }

        $imageSize = $this->getSize()->align($position, $x, $y);
        $otherImageSize = $otherImage->getSize()->align($position);
        $target = $imageSize->relativePosition($otherImageSize);

        imagealphablending($this->image, true);

        imagecopy(
            $this->image,
            $otherImage->image,
            $target->x,
            $target->y,
            0,
            0,
            $otherImageSize->width,
            $otherImageSize->height
        );

        return $this;
    }

    public function resize(int $width, int $height, array $constraints = []): self
    {
        $resized = $this->getSize()->resize($width, $height, $constraints);

        $this->modify($resized->width, $resized->height, 0, 0, $this->getWidth(), $this->getHeight());

        return $this;
    }

    public function width(int $width, array $constraints = [Constraint::PreserveAspectRatio]): self
    {
        $this->resize($width, $this->getHeight(), $constraints);

        return $this;
    }

    public function height(int $height, array $constraints = [Constraint::PreserveAspectRatio]): self
    {
        $this->resize($this->getWidth(), $height, $constraints);

        return $this;
    }

    public function border(int $width, BorderType $type, string $color = '000000'): self
    {
        if ($type === BorderType::Shrink) {
            $originalWidth = $this->getWidth();
            $originalHeight = $this->getHeight();

            $this
                ->resize(
                    (int) round($this->getWidth() - ($width * 2)),
                    (int) round($this->getHeight() - ($width * 2)),
                    [Constraint::PreserveAspectRatio],
                )
                ->resizeCanvas(
                    $originalWidth,
                    $originalHeight,
                    AlignPosition::Center,
                    false,
                    $color,
                );

            return $this;
        }

        if ($type === BorderType::Expand) {
            $this->resizeCanvas(
                (int) round($width * 2),
                (int) round($width * 2),
                AlignPosition::Center,
                true,
                $color,
            );

            return $this;
        }

        if ($type === BorderType::Overlay) {
            $backgroundColor = new GdColor(null);

            imagefilledrectangle(
                $this->image,
                (int) round($width / 2),
                (int) round($width / 2),
                (int) round($this->getWidth() - ($width / 2)),
                (int) round($this->getHeight() - ($width / 2)),
                $backgroundColor->getInt()
            );

            $borderColor = new GdColor($color);

            imagesetthickness($this->image, $width);

            imagerectangle(
                $this->image,
                (int) round($width / 2),
                (int) round($width / 2),
                (int) round($this->getWidth() - ($width / 2)),
                (int) round($this->getHeight() - ($width / 2)),
                $borderColor->getInt()
            );

            return $this;
        }

        return $this;
    }

    /** @param  int<-1, 100>  $quality */
    public function quality(int $quality): self
    {
        $this->ensureNumberBetween($quality, -1, 100, 'quality');
        $this->quality = $quality;

        return $this;
    }

    /** @return int<-1, 9> */
    protected function pngCompression(): int
    {
        if ($this->quality === -1) {
            return -1;
        }

        return (int) round((100 - $this->quality) / 10);
    }

    public function format(string $format): ImageDriver
    {
        switch (strtolower($format)) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($this->image, null, $this->quality);
                break;
            case 'png':
                imagepng($this->image, null, $this->pngCompression());
                break;
            case 'gif':
                imagegif($this->image, null);
                break;
            case 'webp':
                imagewebp($this->image, null);
                break;
            default:
                throw UnsupportedImageFormat::make($format);
        }

        return $this;
    }
}
