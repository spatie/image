<?php

namespace Spatie\Image\Drivers\Vips;

use Jcupitt\Vips\Exception;
use Jcupitt\Vips\Image;
use Spatie\Image\Drivers\Concerns\AddsWatermark;
use Spatie\Image\Drivers\Concerns\CalculatesCropOffsets;
use Spatie\Image\Drivers\Concerns\CalculatesFocalCropCoordinates;
use Spatie\Image\Drivers\Concerns\GetsOrientationFromExif;
use Spatie\Image\Drivers\Concerns\PerformsFitCrops;
use Spatie\Image\Drivers\Concerns\PerformsOptimizations;
use Spatie\Image\Drivers\Concerns\ValidatesArguments;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Drivers\Imagick\ImagickColor;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\BorderType;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\FlipDirection;
use Spatie\Image\Enums\Orientation;
use Spatie\Image\Enums\Unit;
use Spatie\Image\Exceptions\CannotOptimizePng;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Size;

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

    protected ?string $format = null;

    protected int $defaultQuality = 75;

    protected ?int $quality = null;

    /** @var array<string, mixed> */
    protected array $exif = [];

    public function new(int $width, int $height, ?string $backgroundColor = null): static
    {
        $color = new VipsColor($backgroundColor);

        $image = Image::newFromArray(array_fill(0, $height, array_fill(0, $width, $color->getArray())));

        return (new static)->setImage($image);
    }

    protected function setImage(Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function loadFile(string $path, bool $autoRotate = true): static
    {
        $this->image = Image::newFromFile($path);

        $this->setExif($path);

        if ($autoRotate) {
            $this->autoRotate();
        }

        return $this;
    }

    public function driverName(): string
    {
        return 'vips';

    }

    public function save(string $path = ''): static
    {
        if ($this->quality && $this->format === 'png') {
            throw CannotOptimizePng::make();
        }

        $saveProperties = [
            'Q' => $this->quality ?? $this->defaultQuality,
        ];

        try {
            $this->image->writeToFile($path, $saveProperties);
        } catch (Exception $exception) {
            if (str_contains($exception->getMessage(), 'is not a known file format')) {
                throw UnsupportedImageFormat::make($this->format, $exception);
            }

            throw $exception;
        }

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
        $brightness = 1 + ($brightness / 100);

        $this->image = $this->image->linear([$brightness, $brightness, $brightness], [0, 0, 0]);

        return $this;
    }

    public function gamma(float $gamma): static
    {
        // TODO: Implement gamma() method.
    }

    public function contrast(float $level): static
    {
        // TODO: Implement contrast() method.
    }

    public function blur(int $blur): static
    {
        $this->image = $this->image->gaussblur($blur / 10);

        return $this;
    }

    public function colorize(int $red, int $green, int $blue): static
    {
        // TODO: Implement colorize() method.
    }

    public function greyscale(): static
    {
        $this->image = $this->image->colourspace('b-w');

        return $this;
    }

    public function sepia(): static
    {
        /* Implementation from https://github.com/libvips/php-vips/issues/104#issuecomment-686348179 */
        $sepia = Image::newFromArray([
            [0.393, 0.769, 0.189],
            [0.349, 0.686, 0.168],
            [0.272, 0.534, 0.131],
        ]);

        if ($this->image->hasAlpha()) {
            // Separate alpha channel
            $imageWithoutAlpha = $this->image->extract_band(0, ['n' => $this->image->bands - 1]);
            $alpha = $this->image->extract_band($this->image->bands - 1, ['n' => 1]);

            $this->image = $imageWithoutAlpha->recomb($sepia)->bandjoin($alpha);

            return $this;
        }

        $this->image = $this->image->recomb($sepia);

        return $this;
    }

    public function sharpen(float $amount): static
    {
        if ($amount > 0) {
            $this->image = $this->image->sharpen([
                'm2' => $amount,
            ]);
        }

        return $this;
    }

    public function getSize(): Size
    {
        return new Size($this->getWidth(), $this->getHeight());
    }

    public function fit(
        Fit $fit,
        ?int $desiredWidth = null,
        ?int $desiredHeight = null,
        bool $relative = false,
        string $backgroundColor = '#ffffff'
    ): static {
        // TODO: Implement fit() method.
    }

    public function pickColor(int $x, int $y, ColorFormat $colorFormat): mixed
    {
        $colors = $this->image->getpoint($x, $y);

        $color = (new VipsColor())->initFromArray($colors);

        return $color->format($colorFormat);
    }

    public function resizeCanvas(
        ?int $width = null,
        ?int $height = null,
        ?AlignPosition $position = null,
        bool $relative = false,
        string $backgroundColor = '#000000'
    ): static
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
        $contents = base64_encode($this->image->writeToBuffer('.'.$imageFormat));

        if ($prefixWithFormat) {
            return 'data:image/'.$imageFormat.';base64,'.$contents;
        }

        return $contents;
    }

    public function background(string $color): static
    {
        $backgroundColor = new VipsColor($color);

        $background = Image::newFromArray(
            array_fill(0, $this->image->height, array_fill(0, $this->image->width, $backgroundColor->getArray()))
        );

        $this->image = $background->composite2($this->image, 'over');

        return $this;
    }

    public function overlay(ImageDriver $bottomImage, ImageDriver $topImage, int $x, int $y): static
    {
        // TODO: Implement overlay() method.
    }

    public function orientation(?Orientation $orientation = null): static
    {
        $this->image = $this->image->rot($orientation->degrees());

        return $this;
    }

    public function autoRotate(): void
    {
        if (! $this->exif || empty($this->exif['Orientation'])) {
            return;
        }

        switch ($this->exif['Orientation']) {
            case 8:
                $this->image = $this->image->rot90();
                break;
            case 3:
                $this->image = $this->image->rot180();
                break;
            case 5:
            case 7:
            case 6:
                $this->image = $this->image->rot270();
                break;
        }
    }

    public function setExif(string $path): void
    {
        if (! extension_loaded('exif')) {
            return;
        }

        if (! extension_loaded('fileinfo')) {
            return;
        }

        $fInfo = finfo_open(FILEINFO_RAW);
        if ($fInfo) {
            $info = finfo_file($fInfo, $path);
            finfo_close($fInfo);
        }

        if (! isset($info) || ! is_string($info) || ! str_contains($info, 'Exif')) {
            return;
        }

        $result = @exif_read_data($path);

        if (! is_array($result)) {
            $this->exif = [];

            return;
        }

        $this->exif = $result;
    }

    public function exif(): array
    {
        return $this->exif;
    }

    public function flip(FlipDirection $flip): static
    {
        if ($flip === FlipDirection::Both) {
            $this->image = $this->image->flip(FlipDirection::Vertical->value);
            $this->image = $this->image->flip(FlipDirection::Horizontal->value);

            return $this;
        }

        $this->image = $this->image->flip($flip->value);

        return $this;
    }

    public function pixelate(int $pixelate): static
    {
        if ($pixelate === 0) {
            return $this;
        }

        // Resize the image to a smaller size (shrink)
        $this->image = $this->image->resize(1 / $pixelate, ['kernel' => 'nearest']);

        // Resize the image back to the original size (enlarge)
        $this->image = $this->image->resize($pixelate, ['kernel' => 'nearest']);

        return $this;
    }

    public function watermark(ImageDriver|string $watermarkImage, AlignPosition $position = AlignPosition::BottomRight, int $paddingX = 0, int $paddingY = 0, Unit $paddingUnit = Unit::Pixel, int $width = 0, Unit $widthUnit = Unit::Pixel, int $height = 0, Unit $heightUnit = Unit::Pixel, Fit $fit = Fit::Contain, int $alpha = 100): static
    {
        // TODO: Implement watermark() method.
    }

    public function insert(ImageDriver|string $otherImage, AlignPosition $position = AlignPosition::Center, int $x = 0, int $y = 0, int $alpha = 100): static
    {
        // TODO: Implement insert() method.
    }

    public function text(string $text, int $fontSize, string $color = '000000', int $x = 0, int $y = 0, int $angle = 0, string $fontPath = '', int $width = 0): static
    {
        // TODO: Implement text() method.
    }

    public function wrapText(string $text, int $fontSize, string $fontPath = '', int $angle = 0, int $width = 0): string
    {
        // TODO: Implement wrapText() method.
    }

    public function image(): Image
    {
        return $this->image;
    }

    public function resize(int $width, int $height, array $constraints): static
    {
        $resized = $this->getSize()->resize($width, $height, $constraints);

        $widthRatio = $resized->width / $this->image->width;
        $heightRatio = $resized->height / $this->image->height;

        $this->image = $this->image->resize($widthRatio, [
            'vscale' => $heightRatio,
        ]);

        return $this;
    }

    public function width(int $width, array $constraints = []): static
    {
        $newHeight = (int) round($width / $this->getSize()->aspectRatio());

        $this->resize($width, $newHeight, $constraints);

        return $this;
    }

    public function height(int $height, array $constraints = []): static
    {
        $newWidth = (int) round($height * $this->getSize()->aspectRatio());

        $this->resize($newWidth, $height, $constraints);

        return $this;
    }

    public function border(int $width, BorderType $type, string $color = '000000'): static
    {
        // TODO: Implement border() method.
    }

    public function quality(int $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }
}
