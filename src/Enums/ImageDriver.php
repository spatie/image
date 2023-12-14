<?php

namespace Spatie\Image\Enums;

enum ImageDriver: string
{
    case Gd = 'gd';
    case Imagick = 'imagick';
}
