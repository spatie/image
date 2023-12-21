<?php

namespace Spatie\Image\Drivers\Concerns;

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\Unit;

/** @mixin \Spatie\Image\Drivers\ImageDriver */
trait AddsWatermark
{
    public function watermark(
        ImageDriver|string $watermarkImage,
        AlignPosition $position = AlignPosition::BottomRight,
        int $paddingX = 0,
        int $paddingY = 0,
        Unit $paddingUnit = Unit::Pixel,
        int $width = 0,
        Unit $widthUnit = Unit::Pixel,
        int $height = 0,
        Unit $heightUnit = Unit::Pixel,
        Fit $fit = Fit::Contain,
        int $alpha = 100): static
    {
        if (is_string($watermarkImage)) {
            $watermarkImage = (new self())->loadFile($watermarkImage);
        }

        $this->ensureNumberBetween($alpha, 0, 100, 'alpha');

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

        $paddingX = $this->calculateWatermarkX($paddingX, $paddingUnit);
        $paddingY = $this->calculateWatermarkY($paddingY, $paddingUnit);

        $width = $width ? $this->calculateWatermarkX($width, $widthUnit) : null;
        $height = $height ? $this->calculateWatermarkY($height, $widthUnit) : null;

        if (is_null($width) && ! is_null($height)) {
            $watermarkImage->height($height);
        } elseif (! is_null($width) && is_null($height)) {
            $watermarkImage->width($width);
        } else {
            $watermarkImage->fit($fit, $width, $height);
        }

        $this->insert($watermarkImage, $position, $paddingX, $paddingY, $alpha);

        return $this;
    }

    protected function calculateWatermarkX(int $x, Unit $unit): int
    {
        if ($unit === Unit::Percent) {
            return $this->getWidth() * $x / 100;
        }

        return $x;
    }

    protected function calculateWatermarkY(int $y, Unit $unit): int
    {
        if ($unit === Unit::Percent) {
            return $this->getHeight() * $y / 100;
        }

        return $y;
    }
}
