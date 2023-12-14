<?php

namespace Spatie\Image\Drivers;

use ImagickPixel;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Exceptions\InvalidColor;

abstract class Color
{
    public int $red;

    public int $green;

    public int $blue;

    public float $alpha;

    abstract public function initFromInteger(int $value): self;

    /** @param  array<int>  $value */
    abstract public function initFromArray(array $value): self;

    abstract public function initFromString(string $value): self;

    abstract public function initFromObject(ImagickPixel $value): self;

    abstract public function initFromRgb(int $red, int $green, int $blue): self;

    abstract public function initFromRgba(int $red, int $green, int $blue, float $alpha): self;

    abstract public function getInt(): int;

    abstract public function getHex(string $prefix): string;

    /** @return array<int, float|int> */
    abstract public function getArray(): array;

    abstract public function getRgba(): string;

    abstract public function differs(self $color, int $tolerance = 0): bool;

    public function __construct(mixed $value = null)
    {
        $this->parse($value);
    }

    public function parse(mixed $colorValue): self
    {
        match (true) {
            is_string($colorValue) => $this->initFromString($colorValue),
            is_int($colorValue) => $this->initFromInteger($colorValue),
            is_array($colorValue) => $this->initFromArray($colorValue),
            $colorValue instanceof ImagickPixel => $this->initFromObject($colorValue),
            is_null($colorValue) => $this->initFromArray([255, 255, 255, 0]),
            default => throw InvalidColor::make($colorValue),
        };

        return $this;
    }

    public function format(ColorFormat $colorFormat): mixed
    {
        return match ($colorFormat) {
            ColorFormat::Rgba => $this->getRgba(),
            ColorFormat::Hex => $this->getHex('#'),
            ColorFormat::Int => $this->getInt(),
            ColorFormat::Array => $this->getArray(),
            ColorFormat::Object => $this,
        };
    }

    /** @return array<int, float|int> */
    protected function rgbaFromString(string $colorValue): array
    {
        // parse color string in hexidecimal format like #cccccc or cccccc or ccc
        $hexPattern = '/^#?([a-f0-9]{1,2})([a-f0-9]{1,2})([a-f0-9]{1,2})$/i';

        // parse color string in format rgb(140, 140, 140)
        $rgbPattern = '/^rgb ?\(([0-9]{1,3}), ?([0-9]{1,3}), ?([0-9]{1,3})\)$/i';

        // parse color string in format rgba(255, 0, 0, 0.5)
        $rgbaPattern = '/^rgba ?\(([0-9]{1,3}), ?([0-9]{1,3}), ?([0-9]{1,3}), ?([0-9.]{1,4})\)$/i';

        if (preg_match($hexPattern, $colorValue, $matches)) {
            $result = [];
            $result[0] = strlen($matches[1]) == '1' ? (int) hexdec($matches[1].$matches[1]) : (int) hexdec($matches[1]);
            $result[1] = strlen($matches[2]) == '1' ? (int) hexdec($matches[2].$matches[2]) : (int) hexdec($matches[2]);
            $result[2] = strlen($matches[3]) == '1' ? (int) hexdec($matches[3].$matches[3]) : (int) hexdec($matches[3]);
            $result[3] = 1;
        } elseif (preg_match($rgbPattern, $colorValue, $matches)) {
            $result = [];
            $result[0] = ($matches[1] >= 0 && $matches[1] <= 255) ? (int) ($matches[1]) : 0;
            $result[1] = ($matches[2] >= 0 && $matches[2] <= 255) ? (int) ($matches[2]) : 0;
            $result[2] = ($matches[3] >= 0 && $matches[3] <= 255) ? (int) ($matches[3]) : 0;
            $result[3] = 1;
        } elseif (preg_match($rgbaPattern, $colorValue, $matches)) {
            $result = [];
            $result[0] = ($matches[1] >= 0 && $matches[1] <= 255) ? (int) ($matches[1]) : 0;
            $result[1] = ($matches[2] >= 0 && $matches[2] <= 255) ? (int) ($matches[2]) : 0;
            $result[2] = ($matches[3] >= 0 && $matches[3] <= 255) ? (int) ($matches[3]) : 0;
            $result[3] = ($matches[4] >= 0 && $matches[4] <= 1) ? (float) ($matches[4]) : 0;
        } else {
            throw InvalidColor::make($colorValue);
        }

        return $result;
    }
}
