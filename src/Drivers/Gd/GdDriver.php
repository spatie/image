<?php

namespace Spatie\Image\Drivers\Gd;

use Exception;
use GdImage;
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
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\InvalidFont;
use Spatie\Image\Exceptions\MissingParameter;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Point;
use Spatie\Image\Size;
use Throwable;

class GdDriver implements ImageDriver
{
    use AddsWatermark;
    use CalculatesCropOffsets;
    use CalculatesFocalCropAndResizeCoordinates;
    use CalculatesFocalCropCoordinates;
    use GetsOrientationFromExif;
    use PerformsFitCrops;
    use PerformsOptimizations;
    use ValidatesArguments;

    protected GdImage $image;

    protected ?string $format = null;

    /** @var array<string, mixed> */
    protected array $exif = [];

    protected int $quality = -1;

    protected string $originalPath;

    public function new(int $width, int $height, ?string $backgroundColor = null): static
    {
        $image = imagecreatetruecolor($width, $height);

        if (! $image) {
            throw new Exception('Could not create image');
        }

        $backgroundColor = new GdColor($backgroundColor);

        imagefill($image, 0, 0, $backgroundColor->getInt());

        return (new static)->setImage($image);
    }

    protected function setImage(GdImage $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function loadFile(string $path, bool $autoRotate = true): static
    {
        $this->optimize = false;
        $this->quality = -1;
        $this->originalPath = $path;

        $contents = is_file($path) && filesize($path) > 0
            ? file_get_contents($path)
            : '';

        $this->setExif($path);

        try {
            $image = imagecreatefromstring($contents);
        } catch (Throwable $throwable) {
            throw CouldNotLoadImage::make("{$path} : {$throwable->getMessage()}");
        }

        if (! $image) {
            throw CouldNotLoadImage::make($path);
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        $this->image = $image;

        if ($autoRotate) {
            $this->autoRotate();
        }

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

    public function brightness(int $brightness): static
    {
        // TODO: Convert value between -100 and 100 to -255 and 255
        $brightness = round($brightness * 2.55);

        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $brightness);

        return $this;
    }

    public function blur(int $blur): static
    {
        for ($i = 0; $i < $blur; $i++) {
            imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
        }

        return $this;
    }

    public function save(?string $path = null): static
    {
        if (! $path) {
            $path = $this->originalPath;
        }
        if (is_null($this->format)) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
        } else {
            $extension = $this->format;
        }
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
            case 'jfif':
                \imagejpeg($this->image, $path, $this->quality);
                break;
            case 'png':
                \imagepng($this->image, $path, $this->pngCompression());
                break;
            case 'gif':
                \imagegif($this->image, $path);
                break;
            case 'webp':
                $quality = $this->quality === 100 ? IMG_WEBP_LOSSLESS : $this->quality;
                \imagepalettetotruecolor($this->image);
                \imagewebp($this->image, $path, $quality);
                break;
            case 'avif':
                \imageavif($this->image, $path, $this->quality);
                break;
            default:
                throw UnsupportedImageFormat::make($extension);
        }

        if ($this->optimize) {
            $this->optimizerChain->optimize($path);
        }
        $this->format = null;

        return $this;
    }

    public function base64(string $imageFormat = 'jpeg', bool $prefixWithFormat = true): string
    {
        ob_start();

        switch (strtolower($imageFormat)) {
            case 'jpg':
            case 'jpeg':
            case 'jfif':
                \imagejpeg($this->image, null, $this->quality);
                break;
            case 'png':
                \imagepng($this->image, null, $this->pngCompression());
                break;
            case 'gif':
                \imagegif($this->image, null);
                break;
            case 'webp':
                \imagewebp($this->image, null);
                break;
            case 'avif':
                \imageavif($this->image, null);
                break;
            default:
                throw UnsupportedImageFormat::make($imageFormat);
        }

        $imageData = ob_get_contents();
        ob_end_clean();

        if ($prefixWithFormat) {
            return 'data:image/'.$imageFormat.';base64,'.base64_encode($imageData);
        }

        return base64_encode($imageData);
    }

    public function driverName(): string
    {
        return 'gd';
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

        $this->modify(
            $calculatedSize->width,
            $calculatedSize->height,
            0,
            0,
            $this->getWidth(),
            $this->getHeight(),
        );

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

    protected function modify(
        int $desiredWidth,
        int $desiredHeight,
        int $sourceX = 0,
        int $sourceY = 0,
        int $sourceWidth = 0,
        int $sourceHeight = 0,
    ): static {
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
        imagesavealpha($canvas->image, true); // save alpha channel
        imagefilledrectangle($canvas->image, $destinationX, $destinationY, $destinationX + $sourceWidth - 1, $destinationY + $sourceHeight - 1, $transparent);

        // copy image into new canvas
        imagecopy($canvas->image, $this->image, $destinationX, $destinationY, $sourceX, $sourceY, $sourceWidth, $sourceHeight);

        // set new core to canvas
        $this->image = $canvas->image;

        return $this;
    }

    public function gamma(float $gamma): static
    {
        imagegammacorrect($this->image, 1, $gamma);

        return $this;
    }

    public function contrast(float $level): static
    {
        imagefilter($this->image, IMG_FILTER_CONTRAST, ($level * -1));

        return $this;
    }

    public function colorize(int $red, int $green, int $blue): static
    {
        $red = round($red * 2.55);
        $green = round($green * 2.55);
        $blue = round($blue * 2.55);

        imagefilter($this->image, IMG_FILTER_COLORIZE, $red, $green, $blue);

        return $this;
    }

    public function greyscale(): static
    {
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);

        return $this;
    }

    public function manualCrop(int $width, int $height, ?int $x = null, ?int $y = null): static
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

    public function sepia(): static
    {
        return $this
            ->greyscale()
            ->brightness(0)
            ->contrast(5)
            ->colorize(38, 25, 10)
            ->contrast(5);
    }

    public function sharpen(float $amount): static
    {
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

    public function background(string $color): static
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

    public function overlay(ImageDriver $bottomImage, ImageDriver $topImage, int $x = 0, int $y = 0): static
    {
        $bottomImage->insert($topImage, AlignPosition::TopLeft, $x, $y);
        $this->image = $bottomImage->image();

        return $this;
    }

    public function orientation(?Orientation $orientation = null): static
    {
        if (is_null($orientation)) {
            $orientation = $this->getOrientationFromExif($this->exif);
        }

        $this->image = imagerotate($this->image, $orientation->degrees() * -1, 0);

        return $this;
    }

    public function setExif(string $path): void
    {
        if (! extension_loaded('exif') || ! extension_loaded('fileinfo')) {
            return;
        }

        $fInfo = finfo_open(FILEINFO_RAW);
        if (! $fInfo) {
            return;
        }

        $info = finfo_file($fInfo, $path);

        if (! is_string($info) || ! str_contains($info, 'Exif')) {
            return;
        }

        $result = @exif_read_data($path);
        $this->exif = is_array($result) ? $result : [];
    }

    /**
     * @return array<string, mixed>
     */
    public function exif(): array
    {
        return $this->exif;
    }

    public function flip(FlipDirection $flip): static
    {
        $direction = match ($flip) {
            FlipDirection::Horizontal => IMG_FLIP_HORIZONTAL,
            FlipDirection::Vertical => IMG_FLIP_VERTICAL,
            FlipDirection::Both => IMG_FLIP_BOTH,
        };

        imageflip($this->image, $direction);

        return $this;
    }

    public function pixelate(int $pixelate = 50): static
    {
        imagefilter($this->image, IMG_FILTER_PIXELATE, $pixelate, true);

        return $this;
    }

    public function insert(
        ImageDriver|string $otherImage,
        AlignPosition $position = AlignPosition::Center,
        int $x = 0,
        int $y = 0,
        int $alpha = 100
    ): static {
        $this->ensureNumberBetween($alpha, 0, 100, 'alpha');
        if (is_string($otherImage)) {
            $otherImage = (new static)->loadFile($otherImage);
        }

        $imageSize = $this->getSize()->align($position, $x, $y);
        $otherImageSize = $otherImage->getSize()->align($position);
        $target = $imageSize->relativePosition($otherImageSize);

        imagealphablending($this->image, true);
        // check here for the next 3 line https://www.php.net/manual/en/function.imagecopymerge.php#92787
        $cut = imagecreatetruecolor($otherImageSize->width, $otherImageSize->height);
        if (! $cut) {
            throw new Exception('Could not create image');
        }
        imagecopy($cut, $this->image, 0, 0, $target->x, $target->y, $otherImageSize->width, $otherImageSize->height);
        imagecopy($cut, $otherImage->image(), 0, 0, 0, 0, $otherImageSize->width, $otherImageSize->height);

        imagecopymerge(
            $this->image,
            $cut,
            $target->x,
            $target->y,
            0,
            0,
            $otherImageSize->width,
            $otherImageSize->height,
            $alpha
        );

        return $this;
    }

    public function resize(int $width, int $height, array $constraints = []): static
    {
        $resized = $this->getSize()->resize($width, $height, $constraints);

        $this->modify($resized->width, $resized->height, 0, 0, $this->getWidth(), $this->getHeight());

        return $this;
    }

    public function width(int $width, array $constraints = [Constraint::PreserveAspectRatio]): static
    {
        $newHeight = (int) round($width / $this->getSize()->aspectRatio());

        $this->resize($width, $newHeight, $constraints);

        return $this;
    }

    public function height(int $height, array $constraints = [Constraint::PreserveAspectRatio]): static
    {
        $newWidth = (int) round($height * $this->getSize()->aspectRatio());

        $this->resize($newWidth, $height, $constraints);

        return $this;
    }

    public function border(int $width, BorderType $type, string $color = '000000'): static
    {
        imagealphablending($this->image, true);
        imagesavealpha($this->image, true);

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
    public function quality(int $quality): static
    {
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

    public function format(string $format): static
    {
        if (! in_array($format, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'])) {
            throw UnsupportedImageFormat::make($format);
        }
        $this->format = $format;

        return $this;
    }

    public function autoRotate(): void
    {
        if (! $this->exif || empty($this->exif['Orientation'])) {
            return;
        }

        switch ($this->exif['Orientation']) {
            case 8:
                $this->image = imagerotate($this->image, 90, 0);
                break;
            case 3:
                $this->image = imagerotate($this->image, 180, 0);
                break;
            case 5:
            case 7:
            case 6:
                $this->image = imagerotate($this->image, -90, 0);
                break;
        }
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
        $textColor = new GdColor($color);

        if (! $fontPath || ! file_exists($fontPath)) {
            throw InvalidFont::make($fontPath);
        }

        imagettftext(
            $this->image,
            $fontSize,
            $angle,
            $x,
            $y,
            $textColor->getInt(),
            $fontPath,
            $width > 0
                ? $this->wrapText($text, $fontSize, $fontPath, $angle, $width)
                : $text,
        );

        return $this;
    }

    public function wrapText(string $text, int $fontSize, string $fontPath = '', int $angle = 0, int $width = 0): string
    {
        if (! $fontPath || ! file_exists($fontPath)) {
            throw InvalidFont::make($fontPath);
        }

        $wrapped = '';
        $words = explode(' ', $text);

        foreach ($words as $word) {
            $teststring = "{$wrapped} {$word}";

            $testbox = imagettfbbox($fontSize, $angle, $fontPath, $teststring);

            if (! $testbox) {
                $wrapped .= ' '.$word;

                continue;
            }

            if ($testbox[2] > $width) {
                $wrapped .= "\n".$word;
            } else {
                $wrapped .= ' '.$word;
            }
        }

        return $wrapped;
    }
}
