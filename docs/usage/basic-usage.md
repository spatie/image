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

By default, the Imagick driver will be used. The package supports three drivers: **Imagick**, **GD**, and **Vips**.

```php
use Spatie\Image\Image;
use Spatie\Image\Enums\ImageDriver;

// Use GD
Image::useImageDriver(ImageDriver::Gd)->loadFile(string $pathToImage);

// Use Vips (requires libvips and jcupitt/vips package)
Image::useImageDriver(ImageDriver::Vips)->loadFile(string $pathToImage);
```

To use the Vips driver, you need to have [libvips](https://www.libvips.org/) installed on your system and require the `jcupitt/vips` package:

```bash
composer require jcupitt/vips
```

It's also possible to pass an implementation of `ImageDriver` directly. Build your own driver from scratch, or extend one of the provided drivers (`ImagickDriver`, `GdDriver`, or `VipsDriver`).

```php
use Spatie\Image\Image;

Image::useImageDriver(MyDriver::class)->loadFile(string $pathToImage);
```

## Applying manipulations

Any of the [image manipulations](/docs/image/v3/image-manipulations/overview) can be applied to the loaded `Image` by calling the manipulation's method. All image manipulation methods can be chained.

```php
use Spatie\Image\Image;

Image::load('example.jpg')
    ->sepia()
    ->blur(50)
    ->save();
```

![Sepia + blur manipulation](../../images/example-sepia-blur.jpg)

Every manipulation you call will be applied. When calling a manipulation method multiple times each call will be applied immediately.

```php
use Spatie\Image\Image;

// This will lower the brightness first by 40% and then by 20%
Image::load('example.jpg')
    ->brightness(-40)
    ->brightness(-20)
    ->save();
```


## Saving the image

Calling the `save` method on an `Image` will save the modifications to the specified file.

```php
use Spatie\Image\Image;

Image::load('example.jpg')
    ->width(50)
    ->save('modified-example.jpg');
```

To save the image in a different image format or with a different jpeg quality [see saving images](/docs/image/v3/usage/saving-images).

## Retrieve a base64 string

Calling the `base64` method on an `Image` will return a base64 string of the image.

```php
use Spatie\Image\Image;

Image::load('example.jpg')
    ->base64();
```

By default the base64 string will be formatted as a jpeg and will include the mime type. 
You can alter this by passing extra parameters:

```php
Image::load('example.jpg')
    ->base64('jpeg', $prefixWithFormat = false);
```
