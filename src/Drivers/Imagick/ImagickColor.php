<?php

namespace Spatie\Image\Drivers\Imagick;

use Imagick;
use ImagickPixel;
use InvalidArgumentException;
use Spatie\Image\Drivers\Color;

class ImagickColor extends Color
{
    public ImagickPixel $pixel;

    public function initFromInteger(int $value): self
    {
        $alpha = ($value >> 24) & 0xFF;
        $red = ($value >> 16) & 0xFF;
        $green = ($value >> 8) & 0xFF;
        $blue = $value & 0xFF;

        $alpha = $this->rgb2alpha($alpha);

        $this->setPixel($red, $green, $blue, $alpha);

        return $this;
    }

    public function initFromArray(array $value): self
    {
        $value = array_values($value);

        [$red, $green, $blue] = $value;

        $alpha = $value[3] ?? 1;

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
        $red = $this->getRedValue();
        $green = $this->getGreenValue();
        $blue = $this->getBlueValue();
        $alpha = (int) (round($this->getAlphaValue() * 255));

        return ($alpha << 24) + ($red << 16) + ($green << 8) + $blue;
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

    public function differs(Color $color, int $tolerance = 0): bool
    {
        if (! $color instanceof self) {
            throw new InvalidArgumentException('Color must be an instance of '.self::class);
        }

        $colorTolerance = round($tolerance * 2.55);
        $alphaTolerance = round($tolerance);

        $delta = [
            'r' => abs($color->getRedValue() - $this->getRedValue()),
            'g' => abs($color->getGreenValue() - $this->getGreenValue()),
            'b' => abs($color->getBlueValue() - $this->getBlueValue()),
            'a' => abs($color->getAlphaValue() - $this->getAlphaValue()),
        ];

        return
            $delta['r'] > $colorTolerance ||
            $delta['g'] > $colorTolerance ||
            $delta['b'] > $colorTolerance ||
            $delta['a'] > $alphaTolerance;
    }

    public function getRedValue(): int
    {
        return (int) round($this->pixel->getColorValue(Imagick::COLOR_RED) * 255);
    }

    public function getGreenValue(): int
    {
        return (int) round($this->pixel->getColorValue(Imagick::COLOR_GREEN) * 255);
    }

    public function getBlueValue(): int
    {
        return (int) round($this->pixel->getColorValue(Imagick::COLOR_BLUE) * 255);
    }

    public function getAlphaValue(): float
    {
        return round($this->pixel->getColorValue(Imagick::COLOR_ALPHA), 2);
    }

    private function setPixel(
        int|float $red,
        int|float $green,
        int|float $blue,
        int|float|null $alpha = null
    ): ImagickPixel {
        $alpha = is_null($alpha) ? 1 : $alpha;

        return $this->pixel = new ImagickPixel(
            sprintf('rgba(%d, %d, %d, %.2F)', $red, $green, $blue, $alpha)
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
