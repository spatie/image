<?php

namespace Spatie\Image\Drivers;

enum ColorFormat: string
{
    case RGBA = 'rgba';
    case HEX = 'hex';
    case INT = 'int';
    case OBJECT = 'object';
    case ARRAY = 'array';
}
