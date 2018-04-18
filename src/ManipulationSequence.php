<?php

namespace Spatie\Image;

use ArrayIterator;
use IteratorAggregate;

class ManipulationSequence implements IteratorAggregate
{
    /** @var array */
    protected $groups = [];

    public function __construct(array $sequenceArray = [])
    {
        $this->startNewGroup();
        $this->mergeArray($sequenceArray);
    }

    /**
     * @param string $operation
     * @param string $argument
     *
     * @return $this
     */
    public function addManipulation(string $operation, string $argument)
    {
        $lastIndex = count($this->groups) - 1;

        $this->groups[$lastIndex][$operation] = $argument;

        return $this;
    }

    /**
     * @param \Spatie\Image\ManipulationSequence $sequence
     *
     * @return $this
     */
    public function merge(self $sequence)
    {
        $sequenceArray = $sequence->toArray();

        $this->mergeArray($sequenceArray);

        return $this;
    }

    public function mergeArray(array $sequenceArray)
    {
        foreach ($sequenceArray as $group) {
            foreach ($group as $name => $argument) {
                $this->addManipulation($name, $argument);
            }

            if (next($sequenceArray)) {
                $this->startNewGroup();
            }
        }
    }

    /**
     * @return $this
     */
    public function startNewGroup()
    {
        $this->groups[] = [];

        return $this;
    }

    public function toArray(): array
    {
        return $this->getGroups();
    }

    public function getGroups(): array
    {
        return $this->sanitizeManipulationSets($this->groups);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * @param string $manipulationName
     *
     * @return $this
     */
    public function removeManipulation(string $manipulationName)
    {
        foreach ($this->groups as &$group) {
            if (array_key_exists($manipulationName, $group)) {
                unset($group[$manipulationName]);
            }
        }

        return $this;
    }

    public function isEmpty(): bool
    {
        if (count($this->groups) > 1) {
            return false;
        }

        if (count($this->groups[0]) > 0) {
            return false;
        }

        return true;
    }

    protected function sanitizeManipulationSets(array $groups): array
    {
        return array_values(array_filter($groups, function (array $manipulationSet) {
            return count($manipulationSet);
        }));
    }

    /*
    * Determine if the sequences contain a manipulation with the given name.
    */
    public function getFirstManipulationArgument($searchManipulationName)
    {
        foreach ($this->groups as $group) {
            foreach ($group as $name => $argument) {
                if ($name === $searchManipulationName) {
                    return $argument;
                }
            }

            return;
        }
    }

    /*
    * Determine if the sequences contain a manipulation with the given name.
    */
    public function contains($searchManipulationName)
    {
        foreach ($this->groups as $group) {
            foreach ($group as $name => $argument) {
                if ($name === $searchManipulationName) {
                    return true;
                }
            }

            return false;
        }
    }
}
