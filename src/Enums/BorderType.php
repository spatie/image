<?php

namespace Spatie\Image\Enums;

enum BorderType: string
{
    case Expand = 'expand';
    case Overlay = 'overlay';
    case Shrink = 'shrink';
}
