<?php

namespace Spatie\Image\Enums;

enum CropPosition: string
{
    case TopLeft = 'topLeft';
    case Top = 'top';
    case TopRight = 'topRight';
    case Left = 'left';
    case Center = 'center';
    case Right = 'right';
    case BottomLeft = 'bottomLeft';
    case Bottom = 'bottom';
    case BottomRight = 'bottomRight';

    /** @return array<int> */
    public function offsetPercentages(): array
    {
        return match ($this) {
            self::TopLeft => [0, 0],
            self::Top => [50, 0],
            self::TopRight => [100, 0],
            self::Left => [0, 50],
            self::Center => [50, 50],
            self::Right => [100, 50],
            self::BottomLeft => [0, 100],
            self::Bottom => [50, 100],
            self::BottomRight => [100, 100],
        };
    }
}
