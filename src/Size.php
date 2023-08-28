<?php

namespace Spatie\Image;

use Spatie\Image\Enums\Constraint;

class Size
{
    public function __construct(public $width, public $height)
    {
    }

    public function aspectRatio(): float
    {
        return $this->width / $this->height;
    }

    public function resize(int $desiredWidth = null, int $desiredHeight = null, array $constraints = []): self
    {

        // TODO: improve this check and exception
        if ($desiredWidth === null && $desiredHeight === null) {
            throw new \Exception("Width and height can't both be null");
        }

        $dominantWidthSize = clone $this;

        $dominantWidthSize = $dominantWidthSize
            ->resizeHeight($desiredWidth, $desiredHeight, $constraints)
            ->resizeWidth($desiredWidth, $desiredHeight, $constraints);

        $dominantHeightSize = clone $this;
        $dominantHeightSize = $dominantHeightSize
            ->resizeWidth($desiredWidth, $desiredHeight, $constraints)
            ->resizeHeight($desiredWidth, $desiredHeight, $constraints);

        return $dominantHeightSize->fitsInto(new Size($desiredWidth, $desiredHeight))
            ? $dominantHeightSize
            : $dominantWidthSize;
    }

    public function resizeWidth(int $desiredWidth = null, int $desiredHeight = null, array $constraints = []): self
    {
        $originalWidth = $this->width;
        $originalHeight = $this->height;

        if (!is_numeric($desiredWidth)) {
            return $this;
        }

        if (in_array(Constraint::Upsize, $constraints)) {
            $maximumWidth = $desiredWidth;
            $maximumHeight = $desiredHeight;

            $this->width = min($desiredWidth, $maximumWidth);
        } else {
            $this->width = $desiredWidth;
        }

        if (in_array(Constraint::PreserveAspectRatio, $constraints)) {
            $calculatedHeight = max(1, intval(round($this->width / (new Size($originalWidth, $originalHeight))->aspectRatio())));


            if (in_array(Constraint::Upsize, $constraints)) {
                $this->height = $calculatedHeight > $maximumHeight
                    ? $maximumHeight
                    : $calculatedHeight;
            } else {
                $this->height = $calculatedHeight;
            }
        }

        return $this;
    }

    public function resizeHeight(int $desiredWidth = null, int $desiredHeight = null, array $constraints = []): self
    {
        $originalWidth = $this->width;
        $originalHeight = $this->height;

        if (!is_numeric($desiredHeight)) {
            return $this;
        }

        if (in_array(Constraint::Upsize, $constraints)) {
            $maximumHeight = $desiredHeight;
            $maximumWidth = $desiredWidth;

            $this->height = $desiredHeight > $maximumHeight
                ? $maximumHeight
                : $desiredHeight;
        } else {
            $this->height = $desiredHeight;
        }

        if (in_array(Constraint::PreserveAspectRatio, $constraints)) {
            $calculatedWidth = max(1, intval(round($this->height * (new Size($originalWidth, $originalHeight))->aspectRatio())));

            if (in_array(Constraint::Upsize, $constraints)) {
                $this->width = $calculatedWidth > $maximumWidth
                    ? $maximumWidth
                    : $calculatedWidth;
            } else {
                $this->width = $calculatedWidth;
            }
        }

        return $this;
    }

    public function fitsInto(Size $size): bool
    {
        return ($this->width <= $size->width) && ($this->height <= $size->height);
    }
}
