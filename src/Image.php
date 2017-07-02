<?php

namespace Spatie\Image;

use BadMethodCallException;
use ImageOptimizer\OptimizerFactory;
use Spatie\Image\Exceptions\InvalidImageDriver;

/** @mixin \Spatie\Image\Manipulations */
class Image
{
    /** @var string */
    protected $pathToImage;

    /** @var \Spatie\Image\Manipulations */
    protected $manipulations;

    protected $imageDriver = 'gd';

    protected $shouldOptimize = false;
    protected $optimizationOptions = [];

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

    public function getManipulationSequence(): ManipulationSequence
    {
        return $this->manipulations->getManipulationSequence();
    }

    /**
     * @param array $optimizationOptions
     *
     * @return $this
     */
    public function optimize($optimizationOptions = [])
    {
        $this->optimizationOptions = $optimizationOptions;

        $this->shouldOptimize = true;

        return $this;
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

        if ($this->shouldOptimize) {
            $this->performOptimization($outputPath);
        }
    }

    protected function performOptimization($path)
    {
        $factory = new OptimizerFactory($this->optimizationOptions);

        $optimizer = $factory->get();

        $optimizer->optimize($path);
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
