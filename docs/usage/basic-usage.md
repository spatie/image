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

Image::useImageDriver(ImageDriver::Gd)->loadFile(string $pathToImage);
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
