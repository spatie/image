---
title: Image canvas
weight: 5
---

## Background

The `background` method sets the background for transparent images.

The color can be a color name (see [all available color names](https://developer.mozilla.org/en/docs/Web/CSS/color_value#Color_keywords)) or hexadecimal RGB(A).

```php
$image->background('darkgray');
```

![Darkgray background on PNG](../../images/example-background.png)

## Border

The `border` method adds border with a certain `$width`, `$color` and `$borderType` to the `Image`. 

```php
$image->border(15, '007698', Manipulations::BORDER_SHRINK);
```

![Border](../../images/example-border.jpg)

### Border types

#### `Manipulations::BORDER_OVERLAY`

By default the border will be added as an overlay to the image.

#### `Manipulations::BORDER_SHRINK`

The `BORDER_SHRINK` type shrinks the image to fit the border around. The canvas size stays the same.

#### `Manipulations::BORDER_EXPAND`

The `BORDER_EXPAND` type adds the border to the outside of the image and thus expands the canvas.

## Orientation

The `orientation` method can be used to rotate the `Image` `90`, `180` or `270` degrees. 

```php
$image->orientation(Manipulations::ORIENTATION_180);
```

![Border](../../images/example-orientation.jpg)

The accepted values are available as the following constants on the `Manipulations` class:

- `Manipulations::ORIENTATION_AUTO` (default EXIF orientation)
- `Manipulations::ORIENTATION_90`
- `Manipulations::ORIENTATION_180`
- `Manipulations::ORIENTATION_270`

## Flip

Flip/mirror an image 'horizontally', 'vertically' or 'both'.

```php
$image->flip(Manipulations::FLIP_HORIZONTALLY);
```

![Border](../../images/example-flip-horizontally.jpg)

The accepted values are available as the following constants on the `Manipulations` class:

- `Manipulations::FLIP_HORIZONTALLY`
- `Manipulations::FLIP_VERTICALLY`
- `Manipulations::FLIP_BOTH`
