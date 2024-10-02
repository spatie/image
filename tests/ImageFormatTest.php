<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\UnsupportedImageFormat;
use Spatie\Image\Image;

it('can save supported formats', function (ImageDriver $driver, string $format) {

    if ($format === 'avif' && ! avifIsSupported($driver->driverName())) {
        $this->markTestSkipped('avif is not supported on this system');

        return;
    }

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.$format");

    $driver->loadFile(getTestJpg())->save($targetFile);

    $expectedFormat = $format;

    if (in_array($expectedFormat, ['jpg', 'jfif'])) {
        $expectedFormat = 'jpeg';
    }

    expect($targetFile)->toHaveMime("image/$expectedFormat");
})->with('drivers', ['jpeg', 'jpg', 'jfif', 'gif', 'png', 'webp', 'avif']);

it('can save supported formats using format() function', function (ImageDriver $driver, string $format) {
    if ($format === 'avif' && ! avifIsSupported($driver->driverName())) {
        $this->markTestSkipped('avif is not supported on this system');

        return;
    }
    $driver->loadFile(getTestJpg())->format($format)->save($this->tempDir->path("{$driver->driverName()}/format-test.$format"));
})->with('drivers', ['jpeg', 'gif', 'png', 'webp', 'avif'])->throwsNoExceptions();

it('can save tiff', function () {
    $format = 'tiff';
    $driver = Image::useImageDriver('imagick');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.$format");

    $driver->loadFile(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime("image/$format");
})->skipIfImagickDoesNotSupportFormat('tiff');

it('can save heic', function () {
    $format = 'heic';
    $driver = Image::useImageDriver('imagick');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.$format");

    $driver->loadFile(getTestJpg())->save($targetFile);

    expect($targetFile)->toHaveMime("image/$format");
})->skipIfImagickDoesNotSupportFormat('heic');

it('throws an error for unsupported GD image formats', function (string $format) {
    $driver = Image::useImageDriver('gd');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.$format");
    $driver->loadFile(getTestJpg())->save($targetFile);

})->with(['heic', 'tiff'])->throws(UnsupportedImageFormat::class);

it('can not save a bogus extension', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.foobar");

    $driver->loadFile(getTestJpg())->save($targetFile);
})->with('drivers')->throws(UnsupportedImageFormat::class);

it('can store webp palette images', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/palette-webp.webp");

    $driver->loadFile(getTestFile('palette-webp.png'))->save($targetFile);

    expect($targetFile)->toHaveMime("image/webp");
})->with('drivers');
