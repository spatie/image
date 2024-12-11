---
title: Basic usage
weight: 1
---

## Loading the image

Load an image by calling the static `load` method on the `Image` and passing in the `$pathToImage`.

```php
use Spatie\Image\Image;

$image = Image::load(string $pathToImage);
```


## Selecting a driver

By default, the Imagick driver will be used. However if you would like to use GD you can do this by selecting the driver before loading the image.

```php
use Spatie\Image\Image;

$image = Image::load($file)->useImageDriver('imagick'); // for imagick
$image = Image::load($file)->useImageDriver('gd'); // for gd
```


## Applying manipulations

Any of the [image manipulations](/image/v1/image-manipulations/overview) can be applied to the loaded `Image` by calling the manipulation's method. All image manipulation methods can be chained.

```php
use Spatie\Image\Image;

Image::load('example.jpg')
    ->sepia()
    ->blur(50)
    ->save();
```

![Sepia + blur manipulation](../../images/example-sepia-blur.jpg)

## Saving the image

Calling the `save` method on an `Image` will save the modifications to the original file. You can save your modified image by passing a `$outputPath` to the `save` method.

```php
use Spatie\Image\Image;

Image::load('example.jpg')
    ->width(50)
    ->save('modified-example.jpg');
```

To save the image in a different image format or with a different jpeg quality [see saving images](/image/v1/usage/saving-images).
