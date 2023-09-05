<?php

namespace Spatie\Image\Actions;

use Spatie\Image\Enums\Constraint;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Size;

class CalculateFitSizeAction
{
    public function execute(
        int $originalWidth,
        int $originalHeight,
        Fit $fit,
        int $desiredWidth = null,
        int $desiredHeight = null,
    ): Size {
        $desiredWidth ??= $originalWidth;
        $desiredHeight ??= $originalHeight;

        $size = new Size($originalWidth, $originalHeight);

        return match ($fit) {
            Fit::Contain => $size->resize($desiredWidth, $desiredHeight, [Constraint::PreserveAspectRatio]),
            Fit::Fill => $size->resize($desiredWidth, $desiredHeight, [Constraint::PreserveAspectRatio, Constraint::DoNotUpsize]),
            Fit::Max => $size->resize($desiredWidth, $desiredHeight, [Constraint::PreserveAspectRatio]),
        };
    }
}
