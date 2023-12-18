---
title: Optimizing images
weight: 3
---

## Requirements

Optimization of images is done by the underlying [spatie/image-optimizer](https://github.com/spatie/image-optimizer) package. It assumes that there are a few optimization tools, such as [JpegOptim](http://freecode.com/projects/jpegoptim) an [Pngquant](https://pngquant.org/) present on your system. For more info, check out [the relevant docs](https://github.com/spatie/image-optimizer#optimization-tools).

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

You can customize the optimization by passing an [`OptimizerChain`](https://github.com/spatie/image-optimizer#creating-your-own-optimization-chains) instance to the `optimize` method.

For the `OptimizerChain` instance you can also set the maximum of time in seconds that each individual optimizer in a chain can use by calling `setTimeout`. The default value is 60 seconds. Adjusting this setting may be inevitable while working with large images (see e.g. [#187](https://github.com/spatie/image/pull/187)).

```php
$optimizerChain = (new OptimizerChain)
    ->addOptimizer(new Jpegoptim([
        '--strip-all',
        '--all-progressive',
        '-m85'
    ]))
    ->setTimeout(90);

Image::load('example.jpg')
    ->optimize($optimizerChain)
    ->save();
```
