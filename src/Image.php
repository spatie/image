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

    public static function create($pathToImage)
    {
        return new static($pathToImage);
    }

    public function __construct(string $pathToImage)
    {
        $this->pathToImage = $pathToImage;

        $this->manipulations = new Manipulations();
    }

    public function useImageDriver($imageDriver)
    {
        $this->imageDriver = $imageDriver;
    }

    /**
     * @param callable|Manipulations $manipulations
     */
    public function manipulate($manipulations): self
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

    public function save($outputPath = '')
    {
        if ($path = '') {
            $outputPath = $this->pathToImage;
        }

        GlideManipulator::create($this->pathToImage)
            ->useImageDriver($this->imageDriver)
            ->performManipulations($this->manipulations)
            ->save($outputPath);
    }
}
