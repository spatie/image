---
title: Resizing images
weight: 2
---

## Width and height

The width and height of the `Image` can be modified by calling the `width` and `height` functions and passing in the desired dimensions in pixels. The resized image will be contained within the given `$width` and `$height` dimensions respecting the original aspect ratio.

```php
$image->width(int $width);
$image->height(int $height);
```

### Example usage

```php
Image::load('example.jpg')
    ->width(250)
    ->height(250)
    ->save();
```

![Example width 250px](../../images/example-resize-contain.jpg)

## Fit

The `fit` method fits the image within the given `$width` and `$height` dimensions (pixels) using a certain `$fitMethod`.

```php
$image->fit(string $fitMethod, int $width, int $height);
```

The following `$fitMethod`s are available through constants of the `Manipulations` class:

#### `Manipulations::FIT_CONTAIN` (Default)

Resizes the image to fit within the width and height boundaries without cropping, distorting or altering the aspect ratio.

#### `Manipulations::FIT_MAX`

Resizes the image to fit within the width and height boundaries without cropping, distorting or altering the aspect ratio, and will also not increase the size of the image if it is smaller than the output size.

#### `Manipulations::FIT_FILL`

Resizes the image to fit within the width and height boundaries without cropping or distorting the image, and the remaining space is filled with the background color. The resulting image will match the constraining dimensions.

```php
# Example of how to set background colour to fill remaining pixels

$image
    ->fit(Manipulations::FIT_FILL, 497, 290)
    ->background('007698');
```

![Blue background on fit filled JPG](../../images/example-background.png)

#### `Manipulations::FIT_FILL_MAX`

Resizes the image to fit within the width and height boundaries without cropping but upscaling the image if it’s smaller. The finished image will have remaining space on either width or height (except if the aspect ratio of the new image is the same as the old image). The remaining space will be filled with the background color. The resulting image will match the constraining dimensions.


#### `Manipulations::FIT_STRETCH`

Stretches the image to fit the constraining dimensions exactly. The resulting image will fill the dimensions, and will not maintain the aspect ratio of the input image.

#### `Manipulations::FIT_CROP`

Resizes the image to fill the width and height boundaries and crops any excess image data. The resulting image will match the width and height constraints without distorting the image.

### Example usage

```php
Image::load('example.jpg')
    ->fit(Manipulations::FIT_STRETCH, 450, 150)
    ->save();
```

![Fit stretch to 450x150](../../images/example-fit-stretch.jpg)

## Crop

By calling the `crop` method part of the image will be cropped to the given `$width` and `$height` dimensions (pixels). Use the `$cropMethod` to specify which part will be cropped out.

```php
$image->crop(string $cropMethod, int $width, int $height);
```

The following `$cropMethod`s are available through constants of the `Manipulations` class:
`CROP_TOP_LEFT`, `CROP_TOP`, `CROP_TOP_RIGHT`, `CROP_LEFT`, `CROP_CENTER`, `CROP_RIGHT`, `CROP_BOTTOM_LEFT`, `CROP_BOTTOM`, `CROP_BOTTOM_RIGHT`.

### Example usage

```php
Image::load('example.jpg')
    ->crop(Manipulations::CROP_TOP_RIGHT, 250, 250)
    ->save();
```

![Crop top right to 250x250](../../images/example-crop.jpg)

## Focal crop

The `focalCrop` method can be used to crop around an exact position. The center of the crop is controlled by the `$focalX` and `$focalY` values in percent (`0` - `100`).

You can also zoom into your focal point, if needed. Zoom is controlled by a floating point ranging from `1` to `100`. Each step represents a 100% zoom, so passing 2 will be the same as viewing the image at 200%. The suggested range is 1-10.
```php
$image->focalCrop(int $width, int $height, int $focalX, int $focalY, float $zoom = 1);
```

## Manual crop

The `manualCrop` method crops a specific area of the image by specifying the `$startX` and `$startY` positions and the crop's `$width` and `$height` in pixels.

```php
$image->manualCrop(int $width, int $height, int $x, int $y);
```

### Example usage

```php
Image::load('example.jpg')
    ->manualCrop(600, 400, 20, 620)
    ->save();
```

![Manual crop](../../images/example-manual-crop.jpg)
