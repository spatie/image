<?php

namespace Spatie\Image\Enums;

enum CropPosition: string
{
    case CropTopLeft = 'cropTopLeft';
    case CropTop = 'cropTop';
    case CropTopRight = 'cropTopRight';
    case CropLeft = 'cropLeft';
    case CropCenter = 'cropCenter';
    case CropRight = 'cropRight';
    case CropBottomLeft = 'cropBottomLeft';
    case CropBottom = 'cropBottom';
    case CropBottomRight = 'cropBottomRight';

}
