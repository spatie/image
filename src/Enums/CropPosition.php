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

}
