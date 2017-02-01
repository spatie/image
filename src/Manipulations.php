<?php

namespace Spatie\Image;

class Manipulations
{
    const CROP_TOP_LEFT = 'crop-top-left';
    const CROP_TOP = 'crop-top';
    const CROP_TOP_RIGHT = 'crop-top-right';
    const CROP_LEFT = 'crop-left';
    const CROP_CENTER = 'crop-center';
    const CROP_RIGHT = 'crop-right';
    const CROP_BOTTOM_LEFT = 'crop-bottom-left';
    const CROP_BOTTOM = 'crop-bottom';
    const CROP_BOTTOM_RIGHT = 'crop-bottom-right';

    const ORIENTATION_AUTO = 'auto';
    const ORIENTATION_90 = 90;
    const ORIENTATION_180 = 180;
    const ORIENTATION_270 = 270;

    const FIT_CONTAIN = 'contain';
    const FIT_MAX = 'max';
    const FIT_FILL = 'fill';
    const FIT_STRETCH = 'stretch';
    const FIT_CROP = 'crop';

    const BORDER_OVERLAY = 'overlay';
    const BORDER_SHRINK = 'shrink';
    const BORDER_EXPAND = 'expand';

    const FORMAT_JPG = 'jpg';
    const FORMAT_PJPG = 'pjpg';
    const FORMAT_PNG = 'png';
    const FORMAT_GIF = 'gif';

    /** @var array */
    protected $manipulationSets = [];

    public function __construct(array $manipulations)
    {
        $this->manipulationSets = new ManipulationSets();
    }

    /**
     * @param string $orientation
     * @return $this
     */
    public function orientation(string $orientation)
    {
        return $this->setManipulation(func_get_args());
    }

    /**
     * @param string $cropMethod
     * @param int $width
     * @param int $height
     * @return $this
     *
     * @internal param string $method
     */
    public function crop(string $cropMethod, int $width, int $height)
    {
        return $this
            ->setManipulation([$cropMethod], 'crop')
            ->setManipulation([$width], 'width')
            ->setManipulation([$height], 'height');
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function width(int $width)
    {
        return $this->setManipulation(func_get_args());
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function height(int $height)
    {
        return $this->setManipulation(func_get_args());
    }

    public function fit(string $fitMethod, int $width, int $height)
    {
        return $this
            ->setManipulation($fitMethod, 'fit')
            ->setManipulation($width, 'width')
            ->setManipulation($height, 'height');
    }

    public function devicePixelRatio(int $ratio)
    {
        return $this->setManipulation(func_get_args());
    }

    public function brightness(int $brightness)
    {
        return $this->setManipulation(func_get_args());
    }

    public function gamma(float $gamma)
    {
        return $this->setManipulation(func_get_args());
    }

    public function contrast(int $contrast)
    {
        return $this->setManipulation(func_get_args());
    }

    public function sharpen(int $sharpen)
    {
        return $this->setManipulation(func_get_args());
    }

    public function blur(int $blur)
    {
        return $this->setManipulation(func_get_args());
    }

    public function pixelate(int $pixelate)
    {
        return $this->setManipulation(func_get_args());
    }

    public function greyscale()
    {
        return $this->filter('greyscale');
    }

    public function sepia()
    {
        return $this->filter('sepia');
    }

    public function background(string $colorName)
    {
        return $this->setManipulation(func_get_args());
    }

    public function border(int $width, string $color, string $borderType = 'overlay')
    {
        return $this->setManipulation(["{$width},{$color},{$borderType}"], 'border');
    }

    public function quality(int $quality)
    {
        return $this->setManipulation(func_get_args());
    }

    public function format(string $format)
    {
        return $this->setManipulation(func_get_args());
    }

    protected function filter(string $filterName)
    {
        return $this->setManipulation(func_get_args());
    }


    public function apply()
    {
        $this-$this->manipulationSets->startNewSet();
    }


    public function hasManipulation($manipulationName): bool
    {
        return ! is_null($this->getManipulation($manipulationName));
    }

    /**
     * @param string $manipulationName
     * @return arrau|null
     */
    public function getManipulation(string $manipulationName)
    {
        foreach($this->manipulations as $manipulation) {
            if ($manipulation[0] === $manipulationName) {
                return $manipulation;
            }
        }

        return null;
    }



    protected function setManipulation(array $arguments, string $operation = null)
    {
        $operation = $operation ?? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        $this->manipulationSets->addManipulation($operation, $arguments);

        return $this;
    }

    public function mergeManipulations(Manipulations $manipulations)
    {
        $this->manipulations = array_merge($this->manipulations, $manipulations->toArray());

        return $this;
    }

    public function getManipulationSets(): ManipulationSets
    {
        return $this->manipulationSets;
    }
}
