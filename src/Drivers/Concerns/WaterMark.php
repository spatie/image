<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\Unit;

/** @mixin \Spatie\Image\Drivers\ImageDriver */
trait WaterMark
{
    public function watermark(ImageDriver|string $watermark, AlignPosition $position = AlignPosition::BottomRight,
        int $paddingX = 0,
        int $paddingY = 0,
        Unit $paddingUnit = Unit::Pixel,
        int $width = 0,
        Unit $widthUnit = Unit::Pixel,
        int $height = 0,
        Unit $heightUnit = Unit::Pixel,
        Fit $fit = Fit::Contain): static
    {
        if (is_string($watermark)) {
            $watermark = (new self())->loadFile($watermark);
        }
        if ($paddingUnit === Unit::Percent) {
            $this->ensureNumberBetween($paddingX, 0, 100, 'paddingX');
            $this->ensureNumberBetween($paddingY, 0, 100, 'paddingY');
        }
        if ($widthUnit === Unit::Percent) {
            $this->ensureNumberBetween($width, 0, 100, 'width');
        }
        if ($heightUnit === Unit::Percent) {
            $this->ensureNumberBetween($height, 0, 100, 'height');
        }
        $paddingX = $this->calculateX($paddingX, $paddingUnit);
        $paddingY = $this->calculateY($paddingY, $paddingUnit);
        $width = $width ? $this->calculateX($width, $widthUnit) : null;
        $height = $height ? $this->calculateY($height, $widthUnit) : null;
        if (is_null($width) && ! is_null($height)) {
            $watermark->height($height, [$fit]);
        } elseif (! is_null($width) && is_null($height)) {
            $watermark->width($width, [$fit]);
        } else {
            $watermark->fit($fit, $width, $height);
        }
        $this->insert($watermark, $position, $paddingX, $paddingY);

        return $this;
    }

    protected function calculateX(int $x, Unit $unit): int
    {
        if ($unit === Unit::Percent) {
            return $this->getWidth() * $x / 100;
        }

        return $x;
    }

    protected function calculateY(int $y, Unit $unit): int
    {
        if ($unit === Unit::Percent) {
            return $this->getHeight() * $y / 100;
        }

        return $y;
    }
}
