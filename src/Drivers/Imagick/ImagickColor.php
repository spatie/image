<?php

namespace Spatie\Image\Drivers\Imagick;

use Imagick;
use ImagickPixel;
use Spatie\Image\Drivers\BaseColor;

class ImagickColor extends BaseColor
{
    public ImagickPixel $pixel;

    public function initFromInteger(int $value): self
    {
        $a = ($value >> 24) & 0xFF;
        $r = ($value >> 16) & 0xFF;
        $g = ($value >> 8) & 0xFF;
        $b = $value & 0xFF;
        $a = $this->rgb2alpha($a);

        $this->setPixel($r, $g, $b, $a);

        return $this;
    }

    public function initFromArray(array $value): self
    {
        $value = array_values($value);

        if (count($value) == 4) {

            [$red, $green, $blue, $alpha] = $value;

        } elseif (count($value) == 3) {

            // color array without alpha value
            [$red, $green, $blue] = $value;
            $alpha = 1;
        }

        $this->setPixel($red, $green, $blue, $alpha);

        return $this;
    }

    public function initFromString(string $value): self
    {
        if ($color = $this->rgbaFromString($value)) {
            $this->setPixel($color[0], $color[1], $color[2], $color[3]);
        }

        return $this;
    }

    public function initFromObject(ImagickPixel $value): self
    {
        $this->pixel = $value;

        return $this;
    }

    public function initFromRgb(int $red, int $green, int $blue): self
    {
        $this->setPixel($red, $green, $blue);

        return $this;
    }

    public function initFromRgba(int $red, int $green, int $blue, float $alpha): self
    {
        $this->setPixel($red, $green, $blue, $alpha);

        return $this;
    }

    public function getInt(): int
    {
        $r = $this->getRedValue();
        $g = $this->getGreenValue();
        $b = $this->getBlueValue();
        $a = intval(round($this->getAlphaValue() * 255));

        return ($a << 24) + ($r << 16) + ($g << 8) + $b;
    }

    public function getHex(string $prefix = ''): string
    {
        return sprintf('%s%02x%02x%02x', $prefix,
            $this->getRedValue(),
            $this->getGreenValue(),
            $this->getBlueValue()
        );
    }

    public function getArray(): array
    {
        return [
            $this->getRedValue(),
            $this->getGreenValue(),
            $this->getBlueValue(),
            $this->getAlphaValue(),
        ];
    }

    public function getRgba(): string
    {
        return sprintf('rgba(%d, %d, %d, %.2F)',
            $this->getRedValue(),
            $this->getGreenValue(),
            $this->getBlueValue(),
            $this->getAlphaValue()
        );
    }

    public function differs(BaseColor $color, int $tolerance = 0): bool
    {
        $color_tolerance = round($tolerance * 2.55);
        $alpha_tolerance = round($tolerance);

        $delta = [
            'r' => abs($color->getRedValue() - $this->getRedValue()),
            'g' => abs($color->getGreenValue() - $this->getGreenValue()),
            'b' => abs($color->getBlueValue() - $this->getBlueValue()),
            'a' => abs($color->getAlphaValue() - $this->getAlphaValue()),
        ];

        return
            $delta['r'] > $color_tolerance ||
            $delta['g'] > $color_tolerance ||
            $delta['b'] > $color_tolerance ||
            $delta['a'] > $alpha_tolerance;
    }

    public function getRedValue(): int
    {
        return round($this->pixel->getColorValue(Imagick::COLOR_RED) * 255);
    }

    public function getGreenValue(): int
    {
        return round($this->pixel->getColorValue(Imagick::COLOR_GREEN) * 255);
    }

    public function getBlueValue(): int
    {
        return round($this->pixel->getColorValue(Imagick::COLOR_BLUE) * 255);
    }

    public function getAlphaValue(): float
    {
        return round($this->pixel->getColorValue(Imagick::COLOR_ALPHA), 2);
    }

    private function setPixel($r, $g, $b, $a = null): ImagickPixel
    {
        $a = is_null($a) ? 1 : $a;

        return $this->pixel = new \ImagickPixel(
            sprintf('rgba(%d, %d, %d, %.2F)', $r, $g, $b, $a)
        );
    }

    public function getPixel(): ImagickPixel
    {
        return $this->pixel;
    }

    private function rgb2alpha(int $value): float
    {
        return round($value / 255, 2);
    }
}
