<?php

namespace Spatie\Image\Enums;

enum Fit: string
{
    case Contain = 'contain';
    case Max = 'max';
    case Fill = 'fill';
    case Stretch = 'stretch';
    case Crop = 'crop';
    case FitStretch = 'fitStretch';
}
