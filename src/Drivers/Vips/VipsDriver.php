<?php

namespace Spatie\Image\Drivers\Vips;

use Jcupitt\Vips\BandFormat;
use Jcupitt\Vips\Exception;
use Jcupitt\Vips\Image;
use Spatie\Image\Drivers\Concerns\AddsWatermark;
use Spatie\Image\Drivers\Concerns\CalculatesCropOffsets;
use Spatie\Image\Drivers\Concerns\CalculatesFocalCropAndResizeCoordinates;
use Spatie\Image\Drivers\Concerns\CalculatesFocalCropCoordinates;
use Spatie\Image\Drivers\Concerns\GetsOrientationFromExif;
use Spatie\Image\Drivers\Concerns\PerformsFitCrops;
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
use Spatie\Image\Exceptions\CannotOptimizePng;
use Spatie\Image\Exceptions\InvalidFont;
use Spatie\Image\Exceptions\MissingParameter;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Point;
use Spatie\Image\Size;

class VipsDriver implements ImageDriver
{
    use AddsWatermark;
    use CalculatesCropOffsets;
    use CalculatesFocalCropAndResizeCoordinates;
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
        $rgba = $color->getArray();

        // Create a proper sRGB image with the background color
        $image = Image::black($width, $height, ['bands' => 3])
            ->add([$rgba[0], $rgba[1], $rgba[2]])
            ->cast(BandFormat::UCHAR)
            ->copy(['interpretation' => 'srgb']);

        // Add alpha channel if color has alpha
        if (isset($rgba[3]) && $rgba[3] < 255) {
            $alpha = Image::black($width, $height)->add($rgba[3])->cast(BandFormat::UCHAR);
            $image = $image->bandjoin($alpha);
        }

        return (new static)->setImage($image);
    }

    protected function setImage(Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function loadFile(string $path, bool $autoRotate = true): static
    {
        $this->optimize = false;

        // Use 'access' => 'sequential' to avoid libvips file caching issues
        // when the same file path is overwritten between loads
        $this->image = Image::newFromFile($path, ['access' => 'sequential']);

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
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($this->quality && $extension === 'png') {
            throw CannotOptimizePng::make();
        }

        // Q parameter is only supported for JPEG, WebP, AVIF, HEIC
        $formatsWithQuality = ['jpg', 'jpeg', 'webp', 'avif', 'heic', 'heif'];
        $saveProperties = [];

        if (in_array($extension, $formatsWithQuality)) {
            $saveProperties['Q'] = $this->quality ?? $this->defaultQuality;
        }

        try {
            $this->image->writeToFile($path, $saveProperties);
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            if (str_contains($message, 'is not a known file format') ||
                str_contains($message, 'unsupported') ||
                str_contains($message, 'unable to call')) {
                throw UnsupportedImageFormat::make($extension ?: $this->format ?? 'unknown', $exception);
            }

            throw $exception;
        }

        if ($this->optimize) {
            $this->optimizerChain->optimize($path);
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
        $this->image = $this->image->gamma(['exponent' => $gamma]);

        return $this;
    }

    public function contrast(float $level): static
    {
        // Convert level (-100 to 100) to a contrast factor
        // level > 0 increases contrast, level < 0 decreases contrast
        $factor = (100 + $level) / 100;

        // Apply contrast using linear transformation: (pixel - 128) * factor + 128
        $this->image = $this->image->linear([$factor, $factor, $factor], [128 * (1 - $factor), 128 * (1 - $factor), 128 * (1 - $factor)]);

        return $this;
    }

    public function blur(int $blur): static
    {
        $this->image = $this->image->gaussblur($blur / 10);

        return $this;
    }

    public function colorize(int $red, int $green, int $blue): static
    {
        $overlay = Image::black($this->image->width, $this->image->height)
            ->add([$red, $green, $blue])
            ->cast(BandFormat::UCHAR);

        $this->image = $this->image->composite2($overlay, 'add');

        return $this;
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
        if ($fit === Fit::Crop) {
            return $this->fitCrop($fit, $this->getWidth(), $this->getHeight(), $desiredWidth, $desiredHeight);
        }

        if ($fit === Fit::FillMax) {
            if (is_null($desiredWidth) || is_null($desiredHeight)) {
                throw new MissingParameter('Both desiredWidth and desiredHeight must be set when using Fit::FillMax');
            }

            return $this->fitFillMax($desiredWidth, $desiredHeight, $backgroundColor);
        }

        $calculatedSize = $fit->calculateSize(
            $this->getWidth(),
            $this->getHeight(),
            $desiredWidth,
            $desiredHeight
        );

        $widthRatio = $calculatedSize->width / $this->image->width;
        $heightRatio = $calculatedSize->height / $this->image->height;

        $this->image = $this->image->resize($widthRatio, [
            'vscale' => $heightRatio,
        ]);

        if ($fit->shouldResizeCanvas()) {
            $this->resizeCanvas($desiredWidth, $desiredHeight, AlignPosition::Center, $relative, $backgroundColor);
        }

        return $this;
    }

    public function fitFillMax(int $desiredWidth, int $desiredHeight, string $backgroundColor, bool $relative = false): static
    {
        $this->resize($desiredWidth, $desiredHeight, [Constraint::PreserveAspectRatio]);
        $this->resizeCanvas($desiredWidth, $desiredHeight, AlignPosition::Center, $relative, $backgroundColor);

        return $this;
    }

    public function pickColor(int $x, int $y, ColorFormat $colorFormat): mixed
    {
        $colors = $this->image->getpoint($x, $y);

        $color = (new VipsColor)->initFromArray($colors);

        return $color->format($colorFormat);
    }

    public function resizeCanvas(
        ?int $width = null,
        ?int $height = null,
        ?AlignPosition $position = null,
        bool $relative = false,
        string $backgroundColor = '#000000'
    ): static {
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

        // Crop the source image if needed
        $croppedImage = $this->image->crop($sourceX, $sourceY, $sourceWidth, $sourceHeight);

        // Composite the cropped image onto the canvas
        $this->image = $canvas->image->composite2($croppedImage, 'over', [
            'x' => $destinationX,
            'y' => $destinationY,
        ]);

        return $this;
    }

    public function manualCrop(
        int $width,
        int $height,
        ?int $x = 0,
        ?int $y = 0
    ): static {
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

        $this->image = $this->image->crop(
            $position->x,
            $position->y,
            $width,
            $height,
        );

        return $this;
    }

    public function crop(int $width, int $height, CropPosition $position = CropPosition::Center): static
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

    public function focalCrop(int $width, int $height, ?int $cropCenterX = null, ?int $cropCenterY = null): static
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

    public function focalCropAndResize(int $width, int $height, ?int $cropCenterX = null, ?int $cropCenterY = null): static
    {
        [$cropWidth, $cropHeight, $cropX, $cropY] = $this->calculateFocalCropAndResizeCoordinates(
            $width,
            $height,
            $cropCenterX,
            $cropCenterY
        );

        $this->manualCrop($cropWidth, $cropHeight, $cropX, $cropY)
            ->width($width)
            ->height($height);

        return $this;
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
        $rgba = $backgroundColor->getArray();

        // Create background with proper sRGB colorspace
        $background = Image::black($this->image->width, $this->image->height, ['bands' => 3])
            ->add([$rgba[0], $rgba[1], $rgba[2]])
            ->cast(BandFormat::UCHAR)
            ->copy(['interpretation' => 'srgb']);

        // Ensure the current image has an alpha channel for proper compositing
        if (! $this->image->hasAlpha()) {
            $this->image = $this->image->bandjoin(255);
        }

        // Add alpha to background
        $background = $background->bandjoin(255);

        $this->image = $background->composite2($this->image, 'over');

        return $this;
    }

    public function overlay(ImageDriver $bottomImage, ImageDriver $topImage, int $x, int $y): static
    {
        $bottomImage->insert($topImage, AlignPosition::Center, $x, $y);

        $image = $bottomImage->image();
        assert($image instanceof Image);
        $this->image = $image;

        return $this;
    }

    public function orientation(?Orientation $orientation = null): static
    {
        if (is_null($orientation)) {
            /** @var array{'Orientation'?: int} $exif */
            $exif = $this->exif;
            $orientation = $this->getOrientationFromExif($exif);
        }

        $degrees = $orientation->degrees();
        if ($degrees === 0) {
            return $this;
        }

        // Map degrees to vips rotation
        $this->image = match ($degrees) {
            90, -270 => $this->image->rot90(),
            180, -180 => $this->image->rot180(),
            270, -90 => $this->image->rot270(),
            default => $this->image,
        };

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

    public function insert(ImageDriver|string $otherImage, AlignPosition $position = AlignPosition::Center, int $x = 0, int $y = 0, int $alpha = 100): static
    {
        $this->ensureNumberBetween($alpha, 0, 100, 'alpha');

        if (is_string($otherImage)) {
            $otherImage = (new self)->loadFile($otherImage);
        }

        $imageSize = $this->getSize()->align($position, $x, $y);
        $watermarkSize = $otherImage->getSize()->align($position);
        $target = $imageSize->relativePosition($watermarkSize);

        $otherVipsImage = $otherImage->image();
        assert($otherVipsImage instanceof Image);

        // Apply alpha if not 100%
        if ($alpha < 100) {
            $alphaFactor = $alpha / 100;
            if ($otherVipsImage->hasAlpha()) {
                // Multiply existing alpha channel
                $bands = $otherVipsImage->bands;
                $rgb = $otherVipsImage->extract_band(0, ['n' => $bands - 1]);
                $existingAlpha = $otherVipsImage->extract_band($bands - 1);
                $newAlpha = $existingAlpha->linear($alphaFactor, 0);
                $otherVipsImage = $rgb->bandjoin($newAlpha);
            } else {
                // Add alpha channel
                $alphaChannel = Image::black($otherVipsImage->width, $otherVipsImage->height)
                    ->add(255 * $alphaFactor)
                    ->cast(BandFormat::UCHAR);
                $otherVipsImage = $otherVipsImage->bandjoin($alphaChannel);
            }
        }

        $this->image = $this->image->composite2($otherVipsImage, 'over', [
            'x' => $target->x,
            'y' => $target->y,
        ]);

        return $this;
    }

    public function text(string $text, int $fontSize, string $color = '000000', int $x = 0, int $y = 0, int $angle = 0, string $fontPath = '', int $width = 0): static
    {
        if ($fontPath && ! file_exists($fontPath)) {
            throw InvalidFont::make($fontPath);
        }

        $textColor = new VipsColor($color);

        $textOptions = [
            'dpi' => 72,
            'rgba' => true,
        ];

        if ($fontPath) {
            $textOptions['fontfile'] = $fontPath;
        }

        if ($width > 0) {
            $text = $this->wrapText($text, $fontSize, $fontPath, $angle, $width);
            $textOptions['width'] = $width;
        }

        // Create text image using Pango markup for font size
        $markup = sprintf('<span foreground="#%s" size="%d">%s</span>',
            ltrim($color, '#'),
            $fontSize * 1024,
            htmlspecialchars($text)
        );

        $textImage = Image::text($markup, $textOptions);

        // Apply rotation if needed
        if ($angle !== 0) {
            $textImage = $textImage->rotate($angle);
        }

        // Ensure main image has alpha channel for compositing
        if (! $this->image->hasAlpha()) {
            $this->image = $this->image->bandjoin(255);
        }

        // Composite text onto image
        $this->image = $this->image->composite2($textImage, 'over', [
            'x' => $x,
            'y' => $y,
        ]);

        return $this;
    }

    public function wrapText(string $text, int $fontSize, string $fontPath = '', int $angle = 0, int $width = 0): string
    {
        if ($fontPath && ! file_exists($fontPath)) {
            throw InvalidFont::make($fontPath);
        }

        if ($width <= 0) {
            return $text;
        }

        // Simple word wrapping based on estimated character width
        // This is approximate since we don't have access to font metrics
        $avgCharWidth = $fontSize * 0.6; // Approximate average character width
        $charsPerLine = (int) floor($width / $avgCharWidth);

        if ($charsPerLine <= 0) {
            return $text;
        }

        return wordwrap($text, $charsPerLine, "\n", true);
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
            $borderColor = new VipsColor($color);

            // Create a rectangle border by drawing lines on each edge
            $imgWidth = $this->getWidth();
            $imgHeight = $this->getHeight();

            // Create a mask for the inner area (transparent)
            $innerWidth = $imgWidth - ($width * 2);
            $innerHeight = $imgHeight - ($width * 2);

            if ($innerWidth > 0 && $innerHeight > 0) {
                // Create border frame with proper sRGB colorspace
                $rgba = $borderColor->getArray();
                $borderFrame = Image::black($imgWidth, $imgHeight, ['bands' => 3])
                    ->add([$rgba[0], $rgba[1], $rgba[2]])
                    ->cast(BandFormat::UCHAR)
                    ->copy(['interpretation' => 'srgb']);

                $innerMask = Image::black($innerWidth, $innerHeight)->add(0)->cast(BandFormat::UCHAR);
                $outerMask = Image::black($imgWidth, $imgHeight)->add(255)->cast(BandFormat::UCHAR);

                // Embed inner mask in outer mask
                $mask = $outerMask->insert($innerMask, $width, $width);

                // Add alpha channel to border frame
                $borderFrame = $borderFrame->bandjoin($mask);

                // Ensure main image has alpha channel
                if (! $this->image->hasAlpha()) {
                    $this->image = $this->image->bandjoin(255);
                }

                // Composite border onto image
                $this->image = $this->image->composite2($borderFrame, 'over');
            }

            return $this;
        }
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
