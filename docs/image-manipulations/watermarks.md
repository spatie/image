---
title: Watermarks
weight: 8
---

In addition to the insert method, we introduced a new method called `watermark` that can help you manipulate the insert image on the fly.

This method is very useful especially when you want to define a media conversion in [laravel-media-library](https://spatie.be/index.php/docs/laravel-medialibrary/v11/converting-images/defining-conversions).

Adding a watermark to an `Image` is simple:

```php
$image->watermark('watermark.png');
```

![Example](../../images/example-watermark.jpg)


## Watermark position

As you can see in the example above the watermark is placed in the bottom right corner by default. You can change this behavior by passing the desired `AlignPosition` Enum:

### Example usage

```php
$image->watermark('watermark.png',AlignPosition::center);
```

![Example](../../images/example-watermark-position.jpg)


## Watermark padding

Use the `$paddingX` and `$paddingY` params to set the distance from the watermark to the edges of the image. It also accepts a unit param.

By default the padding values are assumed to be pixels. You can however pass in `Unit::
PERCENT` as the `$paddingUnit` to use percentages as padding values.

### Example usage

```php
$image->watermark('watermark.png',
				paddingX:10,
				paddingY:10,
				paddingUnit:Unit::percent); // 10% padding around the watermark
```

![Example](../../images/example-watermark-padding.jpg)

## Watermark size

The width and height of the watermark can be set using the `$width` and `$height` params. You can change the unit of each of them using `$widthUnit` and `$heightUnit`. By default, the values are interpreted in pixels. You can however specify the width or height of the watermark in percentages by setting the `$widthUnit` or `$heightUnit` to `Unit::PERCENT`.

For example, you might want to add the watermark on the entire top half of the image:

```php
$image->watermark('watermark.png',AlignPosition::Top,
	width:100,widthUnit:Unit::Percent,// 100 percent width
	height:50,heightUnit:Unit::Percent);// 50 percent height

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
$image->watermark('watermark.png',alpha:50);
```

![Example](../../images/example-watermark-opacity.jpg)

