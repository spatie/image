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

The `border` method adds border with a certain `$width`, `$borderType` and `$color` to the `Image`. 

```php
$image->border(15, BorderType::Shrink, '007698');
```

![Border](../../images/example-border.jpg)

### Border types

#### `BorderType::Overlay`

By default the border will be added as an overlay to the image.

#### `BorderType::Shrink`

The `Shrink` type shrinks the image to fit the border around. The canvas size stays the same.

#### `BorderType::Expand`

The `Expand` type adds the border to the outside of the image and thus expands the canvas.

## Orientation

The `orientation` method can be used to rotate the `Image` by passing a Orientation enum value. 

```php
$image->orientation(Orientation::Rotate180);
```

When passing no parameters the orientation will be derived from the exif data of the image.

```php
$image->orientation();
```

![Border](../../images/example-orientation.jpg)

The accepted values are:

- `Orientation::Rotate0`
- `Orientation::Rotate90`
- `Orientation::Rotate180`
- `Orientation::Rotate270`

## Flip

Flip/mirror an image 'horizontally', 'vertically' or 'both'.

```php
$image->flip(FlipDirection::Horizontal);
```

![Border](../../images/example-flip-horizontally.jpg)

The accepted values are:

- `FlipDirection::Vertical`
- `FlipDirection::Horizontal`
- `FlipDirection::Both`
