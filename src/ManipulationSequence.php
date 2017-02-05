<?php

namespace Spatie\Image;

use ArrayIterator;
use IteratorAggregate;

class ManipulationSequence implements IteratorAggregate
{
    /** @var array */
    protected $groups = [];

    /** @var bool */
    protected $startNewGroup = true;

    public function __construct(array $sequenceArray = [])
    {
        $this->mergeArray($sequenceArray);
    }

    /**
     * @param string $operation
     * @param string $argument
     *
     * @return static
     */
    public function addManipulation(string $operation, string $argument)
    {
        if ($this->startNewGroup) {
            $this->groups[] = [];
        }

        $lastIndex = count($this->groups) - 1;

        $this->groups[$lastIndex][$operation] = $argument;

        $this->startNewGroup = false;

        return $this;
    }

    /**
     * @param \Spatie\Image\ManipulationSequence $sequence
     *
     * @return static
     */
    public function merge(ManipulationSequence $sequence)
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
     * @return static
     */
    public function startNewGroup()
    {
        $this->startNewGroup = true;

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
        return new ArrayIterator($this->groups);
    }

    /**
     * @param string $manipulationName
     *
     * @return static
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

    protected function sanitizeManipulationSets(array $groups): array
    {
        return array_values(array_filter($groups, function (array $manipulationSet) {
            return count($manipulationSet);
        }));
    }
}
