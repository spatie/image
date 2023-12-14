<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Exceptions\InvalidManipulation;

trait ValidatesArguments
{
    protected function ensureNumberBetween(int|float $value, int|float $min, int|float $max, string $label): void
    {
        if ($value < $min || $value > $max) {
            throw InvalidManipulation::valueNotInRange($label, $value, $min, $max);
        }
    }
}
