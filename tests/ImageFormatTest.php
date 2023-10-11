<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Image;

it('can save supported formats', function (ImageDriver $driver, string $format) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test." . $format);

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime('image/' . $format);
})->with('drivers', ['jpeg', 'gif', 'png', 'webp']);

it('can save a heic with Imagick', function () {
    $driver = Image::useImageDriver('imagick');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.heic");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime('image/heic');
});

it('can save a tiff with Imagick', function () {
    $driver = Image::useImageDriver('imagick');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.tiff");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime('image/tiff');
});

it('can not save a bogus extension', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.foobar");

    $driver->load(getTestJpg())->save($targetFile);
})->with('drivers')->throws(UnsupportedImageFormat::class);
