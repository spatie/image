<?php

namespace Spatie\Image\Enums;

enum FlipDirection: string
{
    case Horizontal = 'horizontal';
    case Vertical = 'vertical';
    case Both = 'both';
}
