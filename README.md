# Manipulate images with an expressive API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/image.svg?style=flat-square)](https://packagist.org/packages/spatie/image)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/spatie/image/run-tests.yml?label=tests)](https://github.com/spatie/image/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/image.svg?style=flat-square)](https://packagist.org/packages/spatie/image)

Image manipulation doesn't have to be hard. Here are a few examples on how this package makes it very easy to manipulate images.

```php
use Spatie\Image\Image;

// modifying the image so it fits in a 100x100 rectangle without altering aspect ratio
Image::load($pathToImage)
   ->width(100)
   ->height(100)
   ->save($pathToNewImage);
   
// overwriting the original image with a greyscale version   
Image::load($pathToImage)
   ->greyscale()
   ->save();
   
// make image darker and save it in low quality
Image::load($pathToImage)
   ->brightness(-30)
   ->quality(25)
   ->save();
   
// rotate the image and sharpen it
Image::load($pathToImage)
   ->orientation(90)
   ->sharpen(15)
   ->save();
```

You'll find more examples in [the full documentation](https://docs.spatie.be/image).

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/image.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/image)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

``` bash
composer require spatie/image
```

Please note that since version 1.5.3 this package requires exif extension to be enabled: http://php.net/manual/en/exif.installation.php

## Usage

Head over to [the full documentation](https://spatie.be/docs/image).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
npm i pixelmatch
composer test
```

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

Large parts of this codebase were copied from [Intervention Image](https://image.intervention.io/v3) by [Oliver Vogel](https://github.com/olivervogel), and modified for readability and to fit our needs.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
