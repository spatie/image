<?php

namespace Spatie\Image;

use BadMethodCallException;
use Spatie\Image\Exceptions\InvalidImageDriver;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Intervention\Image\ImageManagerStatic as InterventionImage;

/** @mixin \Spatie\Image\Manipulations */
class Image
{
    /** @var string */
    protected $pathToImage;

    /** @var \Spatie\Image\Manipulations */
    protected $manipulations;

    protected $imageDriver = 'gd';

    /**
     * @param string $pathToImage
     *
     * @return static
     */
    public static function load(string $pathToImage)
    {
        return new static($pathToImage);
    }

    public function __construct(string $pathToImage)
    {
        $this->pathToImage = $pathToImage;

        $this->manipulations = new Manipulations();
    }

    /**
     * @param string $imageDriver
     *
     * @return $this
     *
     * @throws InvalidImageDriver
     */
    public function useImageDriver(string $imageDriver)
    {
        if (! in_array($imageDriver, ['gd', 'imagick'])) {
            throw InvalidImageDriver::driver($imageDriver);
        }

        $this->imageDriver = $imageDriver;

        return $this;
    }

    /**
     * @param callable|$manipulations
     *
     * @return $this
     */
    public function manipulate($manipulations)
    {
        if (is_callable($manipulations)) {
            $manipulations($this->manipulations);
        }

        if ($manipulations instanceof Manipulations) {
            $this->manipulations->mergeManipulations($manipulations);
        }

        return $this;
    }

    public function __call($name, $arguments)
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

    public function save($outputPath = '')
    {
        if ($outputPath == '') {
            $outputPath = $this->pathToImage;
        }

        $this->addFormatManipulation($outputPath);

        GlideConversion::create($this->pathToImage)
            ->useImageDriver($this->imageDriver)
            ->performManipulations($this->manipulations)
            ->save($outputPath);

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

    protected function performOptimization($path, array $optimizerChainConfiguration)
    {
        $optimizerChain = OptimizerChainFactory::create();

        if (count($optimizerChainConfiguration)) {
            $optimizers = array_map(function (array $optimizerOptions, string $optimizerClassName) {
                return (new $optimizerClassName)->setOptions($optimizerOptions);
            }, $optimizerChainConfiguration, array_keys($optimizerChainConfiguration));

            $optimizerChain->setOptimizers($optimizers);
        }

        $optimizerChain->optimize($path);
    }

    protected function addFormatManipulation($outputPath)
    {
        if ($this->manipulations->hasManipulation('format')) {
            return;
        }

        $inputExtension = strtolower(pathinfo($this->pathToImage, PATHINFO_EXTENSION));
        $outputExtension = strtolower(pathinfo($outputPath, PATHINFO_EXTENSION));

        if ($inputExtension === $outputExtension) {
            return;
        }

        $supportedFormats = ['jpg', 'png', 'gif'];

        if (in_array($outputExtension, $supportedFormats)) {
            $this->manipulations->format($outputExtension);
        }
    }
}
