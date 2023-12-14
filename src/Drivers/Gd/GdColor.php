<?php

namespace Spatie\Image\Drivers\Gd;

use ImagickPixel;
use Spatie\Image\Drivers\Color;
use Spatie\Image\Exceptions\InvalidColor;

class GdColor extends Color
{
    public int $red;

    public int $green;

    public int $blue;

    public float $alpha;

    public function initFromInteger(int $value): self
    {
        $this->alpha = ($value >> 24) & 0xFF;
        $this->red = ($value >> 16) & 0xFF;
        $this->green = ($value >> 8) & 0xFF;
        $this->blue = $value & 0xFF;

        return $this;
    }

    public function initFromArray(array $value): self
    {
        $value = array_values($value);

        if (count($value) === 4) {

            [$red, $green, $blue, $alpha] = $value;
            $this->alpha = $this->alpha2gd($alpha);

        } elseif (count($value) === 3) {

            [$red, $green, $blue] = $value;
            $this->alpha = 0;

        } else {
            throw InvalidColor::make($value);
        }

        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;

        return $this;
    }

    public function initFromString(string $value): self
    {
        if ($color = $this->rgbaFromString($value)) {
            $this->red = (int) $color[0];
            $this->green = (int) $color[1];
            $this->blue = (int) $color[2];
            $this->alpha = $this->alpha2gd($color[3]);
        }

        return $this;
    }

    public function initFromRgb(int $red, int $green, int $blue): self
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = 0;

        return $this;
    }

    public function initFromRgba(int $red, int $green, int $blue, float $alpha = 1): self
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = $this->alpha2gd($alpha);

        return $this;
    }

    public function initFromObject(ImagickPixel $value): never
    {
        throw InvalidColor::cannotConvertImagickColorToGd();
    }

    public function getInt(): int
    {
        return ($this->alpha << 24) + ($this->red << 16) + ($this->green << 8) + $this->blue;
    }

    public function getHex(string $prefix = ''): string
    {
        return sprintf('%s%02x%02x%02x', $prefix, $this->red, $this->green, $this->blue);
    }

    public function getArray(): array
    {
        return [
            $this->red,
            $this->green,
            $this->blue,
            round(1 - $this->alpha / 127, 2),
        ];
    }

    public function getRgba(): string
    {
        return sprintf('rgba(%d, %d, %d, %.2F)',
            $this->red,
            $this->green,
            $this->blue,
            round(1 - $this->alpha / 127, 2),
        );
    }

    public function differs(Color $color, int $tolerance = 0): bool
    {
        $colorTolerance = round($tolerance * 2.55);
        $alphaTolerance = round($tolerance * 1.27);

        $delta = [
            'r' => abs($color->red - $this->red),
            'g' => abs($color->green - $this->green),
            'b' => abs($color->blue - $this->blue),
            'a' => abs($color->alpha - $this->alpha),
        ];

        return
            $delta['r'] > $colorTolerance ||
            $delta['g'] > $colorTolerance ||
            $delta['b'] > $colorTolerance ||
            $delta['a'] > $alphaTolerance;
    }

    private function alpha2gd(float $input): int
    {
        $oldMin = 0;
        $oldMax = 1;

        $newMin = 127;
        $newMax = 0;

        return (int) ceil(((($input - $oldMin) * ($newMax - $newMin)) / ($oldMax - $oldMin)) + $newMin);
    }
}
