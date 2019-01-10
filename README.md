# Manipulate images with an expressive API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/image.svg?style=flat-square)](https://packagist.org/packages/spatie/image)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/image/master.svg?style=flat-square)](https://travis-ci.org/spatie/image)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/image.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/image)
[![StyleCI](https://styleci.io/repos/80513668/shield?branch=master)](https://styleci.io/repos/80513668)
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

Under the hood [Glide](http://glide.thephpleague.com/) by [Jonathan Reinink](https://twitter.com/reinink) is used.

## Installation

You can install the package via composer:

``` bash
composer require spatie/image
```

Please note that since version 1.5.3 this package requires exif extension to be enabled: http://php.net/manual/en/exif.installation.php

## Usage

Head over to [the full documentation](https://docs.spatie.be/image).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

Under the hood [Glide](http://glide.thephpleague.com/) by [Jonathan Reinink](https://twitter.com/reinink) is used. We've based our documentation and docblocks on text found in [the Glide documentation](http://glide.thephpleague.com/)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
