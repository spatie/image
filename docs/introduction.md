---
title: Introduction
weight: 1
---

Image manipulation doesn't have to be hard. This PHP package makes it super easy to apply common manipulations to images like resizing, cropping and adding effects.

For all available manipulations, please see the [overview](/docs/image/v3/image-manipulations/overview).

## Quick examples

For all the examples in this documentation we'll use this beautiful photo of New York:

![Example Image](../images/example.jpg)

### Sepia and blur

By chaining multiple manipulation methods together we can quickly add a nice effect to our image:

```php
Image::load('example.jpg')
    ->sepia()
    ->blur(50)
    ->save();
```

![Sepia + blur manipulation](../images/example-sepia-blur.jpg)

### Cropping the Starbucks storefront

The `manualCrop` method allows you to crop very specific parts of an image:

```php
Image::load('example.jpg')
    ->manualCrop(600, 400, 20, 620)
    ->save();
```

![Crop Starbucks](../images/example-manual-crop.jpg)

### Converting a transparent PNG to JPG

The image is converted to JPG simply by saving it with the correct file extension.

```php
Image::load('github-logo.png')
    ->fit(Fit::Fill, 500, 300)
    ->background('lightblue')
    ->border(15, BorderType::Expand, '007698')
    ->save('example.jpg');
```

![Example PNG to JPG](../images/example-png-to-jpg.jpg)


