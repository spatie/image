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

    public function merge(ManipulationSets $manipulationSets)
    {
        $this->manipulationSets = array_merge($manipulationSets->toArray());
    }

    /**
     * @return static
     */
    public function startNewSet()
    {
        $this->openNewSet = true;

        return $this;
    }

    public function toArray(): array
    {
        return $this->manipulationSets;
    }

    public function getSets(): array
    {
        return $this->manipulationSets;
    }

    public function removeManipulation(string $name)
    {
        foreach($this->manipulationSets as $manipulationSet) {
            if (array_key_exists($name, $manipulationSet)) {
                unset($manipulationSet[$name]);
            }
        }

        return $this;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->manipulationSets);
    }
}