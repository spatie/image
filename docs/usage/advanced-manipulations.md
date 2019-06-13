---
title: Advanced manipulations
weight: 5
---

By default every manipulation will only be applied once to your image. When calling a manipulation method multiple times only the last call will be applied when the image is saved.

### Example usage

```php
// This will only lower the brightness by 20%
Image::load('example.jpg')
    ->brightness(-40)
    ->brightness(-20)
    ->save();
```

![Example](../../images/example-brightness.jpg)

## The `apply` method

The `apply` method will apply all previous manipulations to the image before continuing with the next manipulations.

### Example usage

```php
// This will lower the brightness by 40%, then lower it again by 20%
Image::load('example.jpg')
    ->brightness(-40)
    ->apply()
    ->brightness(-20)
    ->save();
```

![Example](../../images/example-advanced-manipulations.jpg)
