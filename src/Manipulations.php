<?php

namespace Spatie\Image;

class Manipulations
{
    /** @var array */
    protected $manipulations = [];

    /**
     * @param int $blur
     *
     * @return $this
     */
    public function blur(int $blur)
    {
        $this->setManipulation(func_get_args());

        return $this;
    }

    /**
     * @param int $pixelate
     *
     * @return $this
     */
    public function pixelate(int $pixelate)
    {
        $this->setManipulation(func_get_args());

        return $this;
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function width(int $width)
    {
        $this->setManipulation(func_get_args());

        return $this;
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function height(int $height)
    {
        $this->setManipulation(func_get_args());

        return $this;
    }

    protected function setManipulation(array $arguments)
    {
        $callingFunctionName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        $this->manipulations[] = array_merge([$callingFunctionName], $arguments);
    }

    public function toArray(): array
    {
        return $this->manipulations;
    }
}