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

    /** @var \Spatie\Image\ManipulationSequence */
    protected $manipulationSequence;

    public function __construct(array $manipulations = [])
    {
        $this->manipulationSequence = new ManipulationSequence($manipulations);
    }

    /**
     * @param string $orientation
     *
     * @return static
     */
    public function orientation(string $orientation)
    {
        return $this->addManipulation('orientation', $orientation);
    }

    /**
     * @param string $cropMethod
     * @param int $width
     * @param int $height
     *
     * @return static
     */
    public function crop(string $cropMethod, int $width, int $height)
    {
        return $this
            ->addManipulation('crop', $cropMethod)
            ->addManipulation('width', $width)
            ->addManipulation('height', $height);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $focalX Crop center X in percent
     * @param int $focalY Crop center Y in percent
     *
     * @return static
     */
    public function focalCrop(int $width, int $height, int $focalX, int $focalY)
    {
        return $this
            ->addManipulation('crop', "crop-{$focalX}-{$focalY}")
            ->addManipulation('width', $width)
            ->addManipulation('height', $height);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     *
     * @return static
     */
    public function manualCrop(int $width, int $height, int $x, int $y)
    {
        return $this->addManipulation('manualCrop', "{$width},{$height},{$x},{$y}");
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function width(int $width)
    {
        return $this->addManipulation('width', $width);
    }

    /**
     * @param int $height
     *
     * @return static
     */
    public function height(int $height)
    {
        return $this->addManipulation('height', $height);
    }

    /**
     * @param string $fitMethod
     * @param int $width
     * @param int $height
     *
     * @return static
     */
    public function fit(string $fitMethod, int $width, int $height)
    {
        return $this
            ->addManipulation('fit', $fitMethod)
            ->addManipulation('width', $width)
            ->addManipulation('height', $height);
    }

    /**
     * @param int $ratio
     *
     * @return static
     */
    public function devicePixelRatio(int $ratio)
    {
        return $this->addManipulation('devicePixelRatio', $ratio);
    }

    /**
     * @param int $brightness
     *
     * @return static
     */
    public function brightness(int $brightness)
    {
        return $this->addManipulation('brightness', $brightness);
    }

    /**
     * @param float $gamma
     *
     * @return static
     */
    public function gamma(float $gamma)
    {
        return $this->addManipulation('gamma', $gamma);
    }

    /**
     * @param int $contrast
     *
     * @return static
     */
    public function contrast(int $contrast)
    {
        return $this->addManipulation('contrast', $contrast);
    }

    /**
     * @param int $sharpen
     *
     * @return static
     */
    public function sharpen(int $sharpen)
    {
        return $this->addManipulation('sharpen', $sharpen);
    }

    /**
     * @param int $blur
     *
     * @return static
     */
    public function blur(int $blur)
    {
        return $this->addManipulation('blur', $blur);
    }

    /**
     * @param int $pixelate
     *
     * @return static
     */
    public function pixelate(int $pixelate)
    {
        return $this->addManipulation('pixelate', $pixelate);
    }

    /**
     * @return static
     */
    public function greyscale()
    {
        return $this->filter('greyscale');
    }

    /**
     * @return static
     */
    public function sepia()
    {
        return $this->filter('sepia');
    }

    /**
     * @param string $colorName
     *
     * @return static
     */
    public function background(string $colorName)
    {
        return $this->addManipulation('background', $colorName);
    }

    /**
     * @param int $width
     * @param string $color
     * @param string $borderType
     *
     * @return static
     */
    public function border(int $width, string $color, string $borderType = 'overlay')
    {
        return $this->addManipulation('border', "{$width},{$color},{$borderType}");
    }

    /**
     * @param int $quality
     *
     * @return static
     */
    public function quality(int $quality)
    {
        return $this->addManipulation('quality', $quality);
    }

    /**
     * @param string $format
     *
     * @return static
     */
    public function format(string $format)
    {
        return $this->addManipulation('format', $format);
    }

    /**
     * @param string $filterName
     *
     * @return static
     */
    protected function filter(string $filterName)
    {
        return $this->addManipulation('filter', $filterName);
    }

    /**
     * @return static
     */
    public function apply()
    {
        $this->manipulationSequence->startNewGroup();

        return $this;
    }

    public function removeManipulation(string $name)
    {
        $this->manipulationSequence->removeManipulation($name);
    }

    public function hasManipulation(string $manipulationName): bool
    {
        return ! is_null($this->getManipulationArgument($manipulationName));
    }

    /**
     * @param string $manipulationName
     *
     * @return string|null
     */
    public function getManipulationArgument(string $manipulationName)
    {
        foreach ($this->manipulationSequence->getGroups() as $manipulationSet) {
            if (array_key_exists($manipulationName, $manipulationSet)) {
                return $manipulationSet[$manipulationName];
            }
        }
    }

    protected function addManipulation(string $manipulation, string $argument)
    {
        $this->manipulationSequence->addManipulation($manipulation, $argument);

        return $this;
    }

    public function mergeManipulations(Manipulations $manipulations)
    {
        $this->manipulationSequence->merge($manipulations->manipulationSequence);

        return $this;
    }

    public function getManipulationSequence(): ManipulationSequence
    {
        return $this->manipulationSequence;
    }
}
