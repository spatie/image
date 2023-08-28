<?php

namespace Spatie\Image\Enums;

enum Constraint: string
{
    case PreserveAspectRatio = 'preserveAspectRatio';
    case Upsize = 'upsize';
}
