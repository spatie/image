# v2 to v3
V3 is a major rewrite where we removed several dependencies and tried to streamline all manipulations.

Note that some results might be slightly different from previous versions as we made improvement across a number of methods.

## Working wit the Image object.
The `Image` class has been slimmed down to only being responsible for initializing and delegating to the correct driver (GD or Imagick).

As of V3 there are 3 ways to create a new image instance:

```php
// 1. By passing the path to the image in the constructor.
$image = new Image('path/to/image.jpg');

// 2. Using the static load method. 
$image = Image::load('path/to/image.jpg');

// 3. Selecting a driver and then loading the image.
$image = Image::useImageDriver(ImageDriver::Imagick)->load('path/to/image.jpg');
```

## Other changes
- The option to create a manipulations instance has been removed.
- the `border` method now accepts a BorderType enum value, and it's parameters have been reordered.
- the `orientation` method now accepts a Orientation enum value.
