<?php

namespace Spatie\Image\Drivers;

enum ColorFormat: string
{
    case Rgba = 'rgba';
    case Hex = 'hex';
    case Int = 'int';
    case Object = 'object';
    case Array = 'array';
}
