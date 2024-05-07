<?php

namespace Spatie\Image\Enums;

enum Orientation: int
{
    case Rotate0 = 0;
    case Rotate90 = 90;
    case Rotate180 = 180;
    case Rotate270 = 270;

    public function degrees(): int
    {
        return $this->value;
    }
}
