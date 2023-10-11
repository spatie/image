<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Image;

it('can save supported formats', function (ImageDriver $driver, string $format) {

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.$format");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime("image/$format");
})->with('drivers', ['jpeg', 'gif', 'png', 'webp']);

it('can save Imagick specific formats', function (string $format) {
    $driver = Image::useImageDriver('imagick');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.$format");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime("image/$format");
})->with(['heic', 'tiff']);

it('throws an error for unsupported GD image formats', function (string $format) {
    $driver = Image::useImageDriver('gd');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.$format");
    $driver->load(getTestJpg())->save($targetFile);

})->with(['heic', 'tiff'])->throws(UnsupportedImageFormat::class);

it('can not save a bogus extension', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.foobar");

    $driver->load(getTestJpg())->save($targetFile);
})->with('drivers')->throws(UnsupportedImageFormat::class);
