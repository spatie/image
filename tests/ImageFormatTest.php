<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Image;

it('can save a jpeg', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.jpeg");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime('image/jpeg');
})->with('drivers');

it('can save a png', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.png");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime('image/png');
})->with('drivers');

it('can save a webp', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.webp");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime('image/webp');
})->with('drivers');

it('can save a gif', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.gif");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime('image/gif');
})->with('drivers');

it('can save a heic with Imagick', function () {
    $driver = Image::useImageDriver('imagick');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.heic");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime('image/heic');
});

it('can not save a bogus extension', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.foobar");

    $driver->load(getTestJpg())->save($targetFile);
})->with('drivers')->throws(UnsupportedImageFormat::class);
