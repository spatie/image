<?php

namespace Spatie\Image;

use Exception;

/** @mixin \Spatie\Image\Manipulations */
class Image
{
    /** @var string */
    protected $pathToImage;

    /** @var \Spatie\Image\Manipulations */
    protected $manipulations;

    /** @var */
    protected $imageDriver = 'gd';

    public static function load($pathToImage)
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
     */
    public function useImageDriver(string $imageDriver)
    {
        $this->imageDriver = $imageDriver;

        return $this;
    }

    /**
     * @param callable|$manipulations
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
            throw new Exception("Manipulation `{$name}` does not exist");
        }

        $this->manipulations->$name(...$arguments);

        return $this;
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
