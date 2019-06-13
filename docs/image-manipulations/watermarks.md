---
title: Watermarks
weight: 7
---

Adding a watermark to an `Image` is really simple:

```php
$image->watermark('watermark.png');
```

![Example](../../images/example-watermark.jpg)


## Watermark opacity

Usually watermarks are slightly opaque. You can set the opacity of the watermark with the `watermarkOpacity` method. The accepted value is a percentage between `0` and `100` (default).

Changing the opacity of the watermark requires the `imagick` image driver.

```php
$image->watermark('watermark.png')
      ->watermarkOpacity(50);
```

![Example](../../images/example-watermark-opacity.jpg)


## Watermark position

As you can see in the example above the watermark is placed in the bottom right corner by default. This behaviour can be overridden via the `watermarkPosition`. You can use the `POSITION_*` constants on the `Manipulations` class as arguments. 

### Example usage

```php
$image->watermark('watermark.png')
      ->watermarkPosition(Manipulations::POSITION_CENTER);
```

![Example](../../images/example-watermark-position.jpg)


## Watermark padding

Use the `watermarkPadding` method to set the distance from the watermark to the edges of the image. The method accepts a `$paddingX` value, a `$paddingY` value and an optional `$unit`. 

By default the padding values are assumed to be pixels. You can however pass in `Manipulations::UNIT_PERCENT` as the `$unit` to use percentages as padding values.

### Example usage 

```php
$image->watermark('watermark.png')
      ->watermarkPadding(10, 10, Manipulations::UNIT_PERCENT); // 10% padding around the watermark
```

![Example](../../images/example-watermark-padding.jpg)

As a shorthand you can pass in only the `$paddingX` value and it will be used as both the `$paddingX` and `$paddingY` value in pixels:

```php
$image->watermark('watermark.png')
      ->watermarkPadding(50); // 50px padding on all edges
```

## Watermark size

The width and height of the watermark can be set using the `watermarkWidth` and `watermarkHeight` methods. Both methods take two arguments: an integer `$value` and an optional `$unit`. By default the `$value` is interpreted in pixels. You can however specify the width or height of the watermark in percentages by setting the `$unit` to `Manipulations::UNIT_PERCENT`.

For example you might want to add the watermark on the entire top half of the image:

```php
$image->watermark('watermark.png')
      ->watermarkPosition(Manipulations::POSITION_TOP)      // Watermark at the top
      ->watermarkHeight(50, Manipulations::UNIT_PERCENT)    // 50 percent height
      ->watermarkWidth(100, Manipulations::UNIT_PERCENT);   // 100 percent width
```

![Example](../../images/example-watermark-resize.jpg)

As you can see in the example above. The watermark automatically resized itself to be contained within the given dimension but also keep the aspect ratio the same.

### Watermark fit resize

To change the way the watermark is resized within the given boundaries you can use the `watermarkFit` method. This method accepts a `$fitMethod` argument. The following `$fitMethods` are available on the `Manipulations` class as constants:

- `Manipulations::FIT_CONTAIN`
- `Manipulations::FIT_MAX`
- `Manipulations::FIT_FILL`
- `Manipulations::FIT_STRETCH`
- `Manipulations::FIT_CROP`

You can read more about resizing using the fit methods in the [resizing images](/image/v1/image-manipulations/resizing-images) part of the docs.

For example you might want to stretch the watermark over the entire bottom half of the image:

```php
$image->watermark('watermark.png')
      ->watermarkHeight(50, Manipulations::UNIT_PERCENT)
      ->watermarkWidth(100, Manipulations::UNIT_PERCENT)
      ->watermarkFit(Manipulations::FIT_STRETCH);
```

![Example](../../images/example-watermark-resize-stretch.jpg)

_Very pretty._
