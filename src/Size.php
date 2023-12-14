<?php

namespace Spatie\Image;

use InvalidArgumentException;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Constraint;
use Spatie\Image\Exceptions\CannotResize;

class Size
{
    public function __construct(
        public int $width,
        public int $height,
        public Point $pivot = new Point()
    ) {
    }

    public function aspectRatio(): float
    {
        return $this->width / $this->height;
    }

    /** @param  array<Constraint>  $constraints */
    public function resize(
        ?int $desiredWidth = null,
        ?int $desiredHeight = null,
        array $constraints = []
    ): self {
        if ($desiredWidth === null && $desiredHeight === null) {
            throw new InvalidArgumentException("Width and height can't both be null");
        }

        $dominantWidthSize = clone $this;

        $dominantWidthSize = $dominantWidthSize
            ->resizeHeight($desiredHeight, $constraints)
            ->resizeWidth($desiredWidth, $constraints);

        $dominantHeightSize = clone $this;
        $dominantHeightSize = $dominantHeightSize
            ->resizeWidth($desiredWidth, $constraints)
            ->resizeHeight($desiredHeight, $constraints);

        if ($desiredWidth === null) {
            throw CannotResize::invalidWidth();
        }

        if ($desiredHeight === null) {
            throw CannotResize::invalidHeight();
        }

        // @todo desiredWidth and desiredHeight can still be null here, which will cause an error
        return $dominantHeightSize->fitsInto(new self($desiredWidth, $desiredHeight))
            ? $dominantHeightSize
            : $dominantWidthSize;
    }

    /** @param  array<Constraint>  $constraints */
    public function resizeWidth(
        ?int $desiredWidth = null,
        array $constraints = []
    ): self {
        $originalWidth = $this->width;
        $originalHeight = $this->height;

        if (! is_numeric($desiredWidth)) {
            return $this;
        }

        if (in_array(Constraint::DoNotUpsize, $constraints)) {
            $maximumWidth = $this->width;
            $maximumHeight = $this->height;

            $this->width = min($desiredWidth, $maximumWidth);
        } else {
            $this->width = $desiredWidth;
        }

        if (in_array(Constraint::PreserveAspectRatio, $constraints)) {
            $calculatedHeight = max(1, (int) (round($this->width / (new Size($originalWidth, $originalHeight))->aspectRatio())));

            if (in_array(Constraint::DoNotUpsize, $constraints)) {
                $this->height = $calculatedHeight > $maximumHeight
                    ? $maximumHeight
                    : $calculatedHeight;
            } else {
                $this->height = $calculatedHeight;
            }
        }

        return $this;
    }

    /** @param  array<Constraint>  $constraints */
    public function resizeHeight(?int $desiredHeight = null, array $constraints = []): self
    {
        $originalWidth = $this->width;
        $originalHeight = $this->height;

        if (! is_numeric($desiredHeight)) {
            return $this;
        }

        if (in_array(Constraint::DoNotUpsize, $constraints)) {
            $maximumHeight = $this->height;
            $maximumWidth = $this->width;

            $this->height = $desiredHeight > $maximumHeight
                ? $maximumHeight
                : $desiredHeight;
        } else {
            $this->height = $desiredHeight;
        }

        if (in_array(Constraint::PreserveAspectRatio, $constraints)) {
            $calculatedWidth = max(1, (int) (round($this->height * (new Size($originalWidth, $originalHeight))->aspectRatio())));

            if (in_array(Constraint::DoNotUpsize, $constraints)) {
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

    public function align(AlignPosition $position, int $offsetX = 0, int $offsetY = 0): self
    {

        switch ($position) {

            case AlignPosition::Top:
            case AlignPosition::TopCenter:
            case AlignPosition::TopMiddle:
            case AlignPosition::CenterTop:
            case AlignPosition::MiddleTop:
                $x = (int) ($this->width / 2);
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
                $y = (int) ($this->height / 2);
                break;

            case AlignPosition::Right:
            case AlignPosition::RightCenter:
            case AlignPosition::RightMiddle:
            case AlignPosition::CenterRight:
            case AlignPosition::MiddleRight:
                $x = $this->width - $offsetX;
                $y = (int) ($this->height / 2);
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
                $x = (int) ($this->width / 2);
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
                $x = (int) ($this->width / 2) + $offsetX;
                $y = (int) ($this->height / 2) + $offsetY;
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
