<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Drivers\Imagick\ImagickDriver;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Image;

it('can load an image', function () {
    $image = Image::load(getTestJpg());

    expect($image)->toBeInstanceOf(ImageDriver::class);
});

it('can load an image with a custom driver', function () {
    $image = Image::useImageDriver(\Spatie\Image\Enums\ImageDriver::Gd)->load(getTestJpg());

    expect($image)->toBeInstanceOf(ImageDriver::class);
});

it('will use imagick if it is available', function () {
    $image = Image::load(getTestJpg());

    expect($image)->toBeInstanceOf(ImagickDriver::class);
});

it('will throw an exception when no file exists at the given path', function () {
    $invalidPath = getTestJpg().'non-existing';

    Image::load($invalidPath);
})->throws(CouldNotLoadImage::class);
