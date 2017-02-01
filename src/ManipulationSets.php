<?php

namespace Spatie\Image;

use ArrayIterator;
use IteratorAggregate;

class ManipulationSets implements IteratorAggregate
{
    /** @var array */
    protected $manipulationSets = [];

    /** @var bool  */
    protected $startNewSet = true;

    /**
     * @param string $operation
     * @param string $argument
     *
     * @return static
     */
    public function addManipulation(string $operation, string $argument)
    {
        if ($this->startNewSet) {
            $this->manipulationSets[] = [];
        }

        $lastIndex = count($this->manipulationSets) - 1;

        $this->manipulationSets[$lastIndex][$operation] = $argument;

        $this->startNewSet = false;

        return $this;
    }

    public function merge(ManipulationSets $manipulationSets)
    {
        foreach($manipulationSets->toArray() as $manipulationSet) {
            foreach($manipulationSet as $name => $argument) {
                $this->addManipulation($name, $argument);
            }

            if(next($manipulationSets)) {
                $this->startNewSet();
            }
        }
    }

    /**
     * @return static
     */
    public function startNewSet()
    {
        $this->startNewSet = true;

        return $this;
    }

    public function toArray(): array
    {
        return $this->getSets();
    }

    public function getSets(): array
    {
        return $this->sanitizeManipulationSets($this->manipulationSets);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->manipulationSets);
    }

    public function removeManipulation($manipulationName)
    {
        foreach($this->manipulationSets as &$manipulationSet) {
            if (array_key_exists($manipulationName, $manipulationSet)) {
                unset($manipulationSet[$manipulationName]);
            }
        }

        return $this;
    }

    protected function sanitizeManipulationSets(array $manipulationSets): array
    {
        return array_filter($manipulationSets, function(array $manipulationSet) {
            return count($manipulationSet);
        });
    }
}