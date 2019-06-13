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

The `fit` method fits the image within the given `$width` and `$height` dimensions (pixels) using a certain `$cropMethod`.

```php
$image->fit(string $cropMethod, int $width, int $height);
```

The following `$cropMethod`s are available through constants of the `Manipulations` class:

#### `Manipulations::FIT_CONTAIN` 

This is the default fitting method. The image will be resized to be contained within the given dimensions respecting the original aspect ratio.

#### `Manipulations::FIT_MAX`

The image will be resized to be contained within the given dimensions respecting the original aspect ratio and without increasing the size above the original image size.

#### `Manipulations::FIT_FILL`

Like `FIT_CONTAIN` the image will be resized to be contained within the given dimensions respecting the original aspect ratio. The remaining canvas will be filled with a background color.

```php
$image
    ->fit(Manipulations::FIT_FILL, 497, 290)
    ->background('007698');
```

![Blue background on fitted JPG](../../images/example-background.png)

#### `Manipulations::FIT_STRETCH`

The image will be stretched out to the exact dimensions given.

#### `Manipulations::FIT_CROP`

The image will be resized to completely cover the given dimensions respecting the orginal aspect ratio. Some parts of the image may be cropped out.

### Example usage

```php
Image::load('example.jpg')
    ->fit(Manipulations::FIT_STRETCH, 250, 250)
    ->save();
```

![Crop top right to 250x250](../../images/example-fit-stretch.jpg)

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

```php
$image->focalCrop(int $width, int $height, int $focalX, int $focalY);
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
