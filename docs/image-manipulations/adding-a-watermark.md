---
title: Adding a watermark
weight: 8
---

Using the `watermark` method you can easily position and add a watermark. By default, it will be placer in the bottom right corner of the image.

```php
$image->watermark('watermark.png');
```

![Example](../../images/example-watermark.jpg)

## Watermark position

The watermark is placed in the bottom right corner by default. You can change this behavior by passing the desired `AlignPosition` Enum:

### Example usage

```php
$image->watermark('watermark.png', AlignPosition::center);
```

![Example](../../images/example-watermark-position.jpg)


## Watermark padding

Use the `$paddingX` and `$paddingY` params to set the distance from the watermark to the edges of the image. It also accepts a unit param.

By default, the padding values are assumed to be pixels. You can however pass in `Unit::
PERCENT` as the `$paddingUnit` to use percentages as padding values.

### Example usage

```php
$image->watermark('watermark.png',
    paddingX: 10,
    paddingY: 10,
    paddingUnit: Unit::Percent
); // 10% padding around the watermark
```

![Example](../../images/example-watermark-padding.jpg)

## Watermark size

The width and height of the watermark can be set using the `$width` and `$height` params. You can change the unit of each of them using `$widthUnit` and `$heightUnit`. By default, the values are interpreted in pixels. You can however specify the width or height of the watermark in percentages by setting the `$widthUnit` or `$heightUnit` to `Unit::PERCENT`.

For example, you might want to add the watermark on the entire top half of the image:

```php
$image->watermark('watermark.png',
    AlignPosition::Top,
	width: 100,
	widthUnit: Unit::Percent,
	height: 50,
	heightUnit: Unit::Percent
);
```

![Example](../../images/example-watermark-resize.jpg)

As you can see in the example above. The watermark automatically resized itself to be contained within the given dimension but also kept the aspect ratio the same.

### Watermark fit resize

To change the way the watermark is resized within the given boundaries you can use the `$fit` param. This param accepts any `Fit` enum value.

You can read more about resizing using the fit methods in the [resizing images](/image/v3/image-manipulations/resizing-images) part of the docs.

For example, you might want to stretch the watermark over the entire bottom half of the image:

```php
$image->watermark('watermark.png',AlignPosition::Top,
	width:100,widthUnit:Unit::Percent,
	height:50,heightUnit:Unit::Percent,
	fit: Fit::Stretch);
```

![Example](../../images/example-watermark-resize-stretch.jpg)

## Watermark opacity

Usually, watermarks are slightly opaque. You can set the opacity of the watermark with the `$alpha` param. The accepted value is a percentage between `0` and `100` (default).

```php
$image->watermark('watermark.png',alpha: 50);
```

![Example](../../images/example-watermark-opacity.jpg)

