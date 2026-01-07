---
title: Installation and setup
weight: 3
---

## Basic installation

You can install this package via composer using:

``` bash
composer require spatie/image
```

The package supports three image drivers: **Imagick** (default), **GD**, and **Vips**.

## Using the Vips driver

To use the Vips driver, you need to have [libvips](https://www.libvips.org/) installed on your system. Then require the PHP bindings:

```bash
composer require jcupitt/vips
```

Vips is a fast image processing library that uses less memory than Imagick or GD, making it ideal for processing large images or high-volume image processing.
