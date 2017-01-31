<?php

namespace Spatie\Image;

class Image
{
    /** @var string */
    protected $pathToImage;

    /** @var \Spatie\Image\Manipulations  */
    protected $manipulations;

    /** @var  */
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
        if (is_callable($manipulationConfiguration = $manipulations)) {
            $manipulations = new Manipulations();

            $manipulationConfiguration($manipulations);
        }

        $this->manipulations = $manipulations;

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