<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Exceptions\InvalidManipulation;

trait ValidatesArguments
{
    protected function ensureNumberBetween(int $value, int $min, int $max, string $label): void
    {
        if ($value < $min || $value > $max) {
            throw InvalidManipulation::valueNotInRange($label, $value, $min, $max);
        }
    }
}
