<?php

namespace Spatie\Image;

use Exception;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Constraint;

class Size
{
    public function __construct(
        public $width,
        public $height,
        public $pivot = new Point()
    )
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
            throw new Exception("Width and height can't both be null");
        }

        $dominantWidthSize = clone $this;

        $dominantWidthSize = $dominantWidthSize
            ->resizeHeight($desiredWidth, $desiredHeight, $constraints)
            ->resizeWidth($desiredWidth, $desiredHeight, $constraints);

        $dominantHeightSize = clone $this;
        $dominantHeightSize = $dominantHeightSize
            ->resizeWidth($desiredWidth, $desiredHeight, $constraints)
            ->resizeHeight($desiredWidth, $desiredHeight, $constraints);

        $result =  $dominantHeightSize->fitsInto(new Size($desiredWidth, $desiredHeight))
            ? $dominantHeightSize
            : $dominantWidthSize;

        return $result;
    }

    public function resizeWidth(int $desiredWidth = null, int $desiredHeight = null, array $constraints = []): self
    {
        $originalWidth = $this->width;
        $originalHeight = $this->height;

        if (! is_numeric($desiredWidth)) {
            return $this;
        }

        if (in_array(Constraint::Upsize, $constraints)) {
            $maximumWidth = $this->width;
            $maximumHeight = $this->height;

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

        if (! is_numeric($desiredHeight)) {
            return $this;
        }

        if (in_array(Constraint::Upsize, $constraints)) {
            // TODO: is this correct?
            $maximumHeight = $this->height;
            $maximumWidth = $this->width;

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

    public function align(AlignPosition $position, $offsetX = 0, $offsetY = 0): self
    {

        switch ($position) {

            case AlignPosition::Top:
            case AlignPosition::TopCenter:
            case AlignPosition::TopMiddle:
            case AlignPosition::CenterTop:
            case AlignPosition::MiddleTop:
                $x = intval($this->width / 2);
                $y = 0 + $offsetY;
                break;

            case AlignPosition::TopRight:
            case AlignPosition::RightTop:
                $x = $this->width - $offsetX;
                $y = 0 + $offsetY;
                break;

            case AlignPosition::Left:
            case AlignPosition::LeftCenter:
            case AlignPosition::LeftMiddle:
            case AlignPosition::CenterLeft:
            case AlignPosition::MiddleLeft:
                $x = 0 + $offsetX;
                $y = intval($this->height / 2);
                break;

            case AlignPosition::Right:
            case AlignPosition::RightCenter:
            case AlignPosition::RightMiddle:
            case AlignPosition::CenterRight:
            case AlignPosition::MiddleRight:
                $x = $this->width - $offsetX;
                $y = intval($this->height / 2);
                break;

            case AlignPosition::BottomLeft:
            case AlignPosition::LeftBottom:
                $x = 0 + $offsetX;
                $y = $this->height - $offsetY;
                break;

            case AlignPosition::Bottom:
            case AlignPosition::BottomCenter:
            case AlignPosition::BottomMiddle:
            case AlignPosition::CenterBottom:
            case AlignPosition::MiddleBottom:
                $x = intval($this->width / 2);
                $y = $this->height - $offsetY;
                break;

            case AlignPosition::BottomRight:
            case AlignPosition::RightBottom:
                $x = $this->width - $offsetX;
                $y = $this->height - $offsetY;
                break;

            case AlignPosition::Center:
            case AlignPosition::Middle:
            case AlignPosition::CenterCenter:
            case AlignPosition::MiddleMiddle:
                $x = intval($this->width / 2) + $offsetX;
                $y = intval($this->height / 2) + $offsetY;
                break;

            default:
            case 'top-left':
            case 'left-top':
                $x = 0 + $offsetX;
                $y = 0 + $offsetY;
                break;
        }

        $this->pivot->setCoordinates($x, $y);

        return $this;
    }

    public function relativePosition(Size $size): Point
    {
        $x = $this->pivot->x - $size->pivot->x;
        $y = $this->pivot->y - $size->pivot->y;

        return new Point($x, $y);
    }
}
