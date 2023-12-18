<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Enums\Unit;

/** @mixin \Spatie\Image\Drivers\ImageDriver */
trait InsertManipulations
{
    protected int $paddingX = 0;

    protected int $paddingY = 0;

    protected $paddingUnit = Unit::PX;

    public function insertPadding(int $x = 0, int $y = 0, Unit $unit = Unit::PX): static
    {
        if ($unit === Unit::Percent) {
            $this->ensureNumberBetween($x, 0, 100, 'paddingX');
            $this->ensureNumberBetween($y, 0, 100, 'paddingY');
        }
        $this->paddingX = $x;
        $this->paddingY = $y;
        $this->paddingUnit = $unit;

        return $this;
    }

    protected function calculatePaddingX(): int
    {
        if ($this->paddingUnit === Unit::Percent) {
            return $this->getWidth() * $this->paddingX / 100;
        }

        return $this->paddingX;
    }

    protected function calculatePaddingY(): int
    {
        if ($this->paddingUnit === Unit::Percent) {
            return $this->getHeight() * $this->paddingY / 100;
        }

        return $this->paddingY;
    }

    public function resetInsertPadding(): static
    {
        $this->paddingX = 0;
        $this->paddingY = 0;
        $this->paddingUnit = Unit::PX;

        return $this;
    }
}
