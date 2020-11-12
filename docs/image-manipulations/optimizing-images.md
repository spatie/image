---
title: Optimizing images
weight: 3
---

## Requirements

Optimization of images is done by the underlying [spatie/image-optimizer](https://github.com/spatie/image-optimizer). It assumes that there are a few optimization tools, such as [JpegOptim](http://freecode.com/projects/jpegoptim) an [Pngquant](https://pngquant.org/) present on your system. For more info, check out [the relevant docs](https://github.com/spatie/image-optimizer#optimization-tools).

## How to use

To shave off some kilobytes of the images the package can optimize images by calling the `optimize` method.

Here's the original image of New York used in all examples has a size of 622 Kb. Let's optimize it.

```php
Image::load('example.jpg')
    ->optimize()
    ->save('example-optimized.jpg');
```

![Optimized Image](../../images/example-optimized.jpg).

The size of the optimized image is 573 Kb.

No matter where or how many times you call `optimize` in you chain, it will always be performed as the last operation once.


## Customizing the optimization

To optimization of images is done by the underlying [spatie/image-optimizer](https://github.com/spatie/image-optimizer) package. You can pass your own customized chains as array. The keys should be fully qualified class names of optimizers and the values the options that they should get. Here's an example

```php
Image::load('example.jpg')
    ->optimize([Jpegoptim::class => [
        '--all-progressive',
    ]])
    ->save();
```
