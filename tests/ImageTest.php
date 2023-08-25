<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Image;

it('can load an image', function() {
    $image = Image::load(getTestJpg());

    expect($image)->toBeInstanceOf(ImageDriver::class);
});

it('will throw an exception when no file exists at the given path', function() {
    $invalidPath = getTestJpg() . 'non-existing';

    Image::load($invalidPath);
})->throws(CouldNotLoadImage::class);
