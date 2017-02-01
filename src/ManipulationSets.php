<?php

namespace Spatie\Image;

use ArrayIterator;
use IteratorAggregate;

class ManipulationSets implements IteratorAggregate
{
    /** @var array */
    protected $manipulationSets = [];

    /** @var bool  */
    protected $openNewSet = true;

    /**
     * @param string $operation
     * @param string $argument
     *
     * @return static
     */
    public function addManipulation(string $operation, string $argument)
    {
        if ($this->openNewSet) {
            $this->manipulationSets[] = [];
        }

        $lastIndex = count($this->manipulationSets) - 1;

        $this->manipulationSets[$lastIndex][$operation] = $argument;

        $this->openNewSet = false;

        return $this;
    }

    /**
     * @return static
     */
    public function startNewSet()
    {
        $this->openNewSet = true;

        return $this;
    }

    public function getSets(): array
    {
        return $this->manipulationSets;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->manipulationSets);
    }
}