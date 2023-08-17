<?php

namespace Spatie\Image;

use BadMethodCallException;
use Intervention\Image\ImageManagerStatic as InterventionImage;
use Spatie\Image\Exceptions\InvalidImageDriver;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\BaseOptimizer;

/**
 * @method static Image orientation(string $orientation)
 * @method static Image flip(string $orientation)
 * @method static Image crop(string $cropMethod, int $width, int $height)
 * @method static Image focalCrop(int $width, int $height, int $focalX, int $focalY, float $zoom = 1)
 * @method static Image manualCrop(int $width, int $height, int $x, int $y)
 * @method static Image width(int $width)
 * @method static Image height(int $height)
 * @method static Image fit(string $fitMethod, ?int $width = null, ?int $height = null)
 * @method static Image devicePixelRatio(int $ratio)
 * @method static Image brightness(int $brightness)
 * @method static Image gamma(float $gamma)
 * @method static Image contrast(int $contrast)
 * @method static Image sharpen(int $sharpen)
 * @method static Image blur(int $blur)
 * @method static Image pixelate(int $pixelate)
 * @method static Image greyscale()
 * @method static Image sepia()
 * @method static Image background(string $colorName)
 * @method static Image border(int $width, string $color, string $borderType = 'overlay')
 * @method static Image quality(int $quality)
 * @method static Image format(string $format)
 * @method static Image filter(string $filterName)
 * @method static Image watermark(string $filePath)
 * @method static Image watermarkWidth(int $width, string $unit = 'px')
 * @method static Image watermarkHeight(int $height, string $unit = 'px')
 * @method static Image watermarkFit(string $fitMethod)
 * @method static Image watermarkPadding(int $xPadding, int $yPadding = null, string $unit = 'px')
 * @method static Image watermarkPosition(string $position)
 * @method static Image watermarkOpacity(int $opacity)
 * @method static Image optimize(array $optimizationOptions = [])
 * @method static Image apply()
 * @method static Image addManipulation(string $manipulationName, string $manipulationArgument)
 * @method static Image mergeManipulations(self $manipulations)
 * @method static Image optimize(array $optimizationOptions = [])
 * @method static Image removeManipulation(string $name)
 * @method static bool hasMultipleConversions(array $manipulations)
 * @method static bool hasManipulation(string $manipulationName)
 * @method static mixed getManipulationArgument(string $manipulationName)
 * @method static ManipulationSequence getManipulationSequence()
 * @method static bool validateManipulation(string $value, string $constantNamePrefix)
 * @method static array getValidManipulationOptions(string $manipulation)
 * @method static mixed getFirstManipulationArgument(string $manipulationName)
 */
class Image
{
    protected Manipulations $manipulations;

    protected string $imageDriver = 'gd';

    protected ?string $temporaryDirectory = null;

    protected ?OptimizerChain $optimizerChain = null;

    public function __construct(protected string $pathToImage)
    {
        $this->manipulations = new Manipulations();
    }

    public static function load(string $pathToImage): static
    {
        return new static($pathToImage);
    }

    public function setTemporaryDirectory($tempDir): static
    {
        $this->temporaryDirectory = $tempDir;

        return $this;
    }

    public function setOptimizeChain(OptimizerChain $optimizerChain): static
    {
        $this->optimizerChain = $optimizerChain;

        return $this;
    }

    /**
     * @param string $imageDriver
     * @return $this
     * @throws InvalidImageDriver
     */
    public function useImageDriver(string $imageDriver): static
    {
        if (! in_array($imageDriver, ['gd', 'imagick'])) {
            throw InvalidImageDriver::driver($imageDriver);
        }

        $this->imageDriver = $imageDriver;

        InterventionImage::configure([
            'driver' => $this->imageDriver,
        ]);

        return $this;
    }

    public function manipulate(callable | Manipulations $manipulations): static
    {
        if (is_callable($manipulations)) {
            $manipulations($this->manipulations);
        }

        if ($manipulations instanceof Manipulations) {
            $this->manipulations->mergeManipulations($manipulations);
        }

        return $this;
    }

    public function __call($name, $arguments): static
    {
        if (! method_exists($this->manipulations, $name)) {
            throw new BadMethodCallException("Manipulation `{$name}` does not exist");
        }

        $this->manipulations->$name(...$arguments);

        return $this;
    }

    public function getWidth(): int
    {
        return InterventionImage::make($this->pathToImage)->width();
    }

    public function getHeight(): int
    {
        return InterventionImage::make($this->pathToImage)->height();
    }

    public function getManipulationSequence(): ManipulationSequence
    {
        return $this->manipulations->getManipulationSequence();
    }

    public function save(string $outputPath = ''): void
    {
        if ($outputPath === '') {
            $outputPath = $this->pathToImage;
        }

        $this->addFormatManipulation($outputPath);

        $glideConversion = GlideConversion::create($this->pathToImage)->useImageDriver($this->imageDriver);

        if (! is_null($this->temporaryDirectory)) {
            $glideConversion->setTemporaryDirectory($this->temporaryDirectory);
        }

        $glideConversion->performManipulations($this->manipulations);

        $glideConversion->save($outputPath);

        if ($this->shouldOptimize()) {
            $optimizerChainConfiguration = $this->manipulations->getFirstManipulationArgument('optimize');

            $optimizerChainConfiguration = json_decode($optimizerChainConfiguration, true);

            $this->performOptimization($outputPath, $optimizerChainConfiguration);
        }
    }

    protected function shouldOptimize(): bool
    {
        return ! is_null($this->manipulations->getFirstManipulationArgument('optimize'));
    }

    protected function performOptimization($path, array $optimizerChainConfiguration): void
    {
        $optimizerChain = $this->optimizerChain ?? OptimizerChainFactory::create();

        if (count($optimizerChainConfiguration)) {
            $existingOptimizers = $optimizerChain->getOptimizers();

            $optimizers = array_map(function (array $optimizerOptions, string $optimizerClassName) use ($existingOptimizers) {
                $optimizer = array_values(array_filter($existingOptimizers, function ($optimizer) use ($optimizerClassName) {
                    return $optimizer::class === $optimizerClassName;
                }));

                $optimizer = isset($optimizer[0]) && $optimizer[0] instanceof BaseOptimizer ? $optimizer[0] : new $optimizerClassName();

                return $optimizer->setOptions($optimizerOptions)->setBinaryPath($optimizer->binaryPath);
            }, $optimizerChainConfiguration, array_keys($optimizerChainConfiguration));

            $optimizerChain->setOptimizers($optimizers);
        }

        $optimizerChain->optimize($path);
    }

    protected function addFormatManipulation($outputPath): void
    {
        if ($this->manipulations->hasManipulation('format')) {
            return;
        }

        $inputExtension = strtolower(pathinfo($this->pathToImage, PATHINFO_EXTENSION));
        $outputExtension = strtolower(pathinfo($outputPath, PATHINFO_EXTENSION));

        if ($inputExtension === $outputExtension) {
            return;
        }

        $supportedFormats = [
            Manipulations::FORMAT_JPG,
            Manipulations::FORMAT_PJPG,
            Manipulations::FORMAT_PNG,
            Manipulations::FORMAT_GIF,
            Manipulations::FORMAT_WEBP,
            Manipulations::FORMAT_AVIF,
        ];
        //gd driver doesn't support TIFF
        if ($this->imageDriver === 'imagick') {
            $supportedFormats[] = Manipulations::FORMAT_TIFF;
        }

        if (in_array($outputExtension, $supportedFormats)) {
            $this->manipulations->format($outputExtension);
        }
    }
}
