<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\InvalidImageDriver;
use Spatie\Image\Image;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can load an image', function () {
    $image = Image::load(getTestJpg());

    expect($image)->toBeInstanceOf(ImageDriver::class);
});

it('can load an image with a custom driver', function () {
    $image = Image::useImageDriver(\Spatie\Image\Enums\ImageDriver::Gd)->loadFile(getTestJpg());

    expect($image)->toBeInstanceOf(ImageDriver::class);
});

it('will use imagick if it is available', function () {
    $image = Image::load(getTestJpg());

    expect($image->driverName())->toEqual('imagick');
});

it('it can load image from file', function () {
    $image = Image::load(getTestJpg());

    expect($image->getHeight())->toEqual(280);
});

it('will throw an exception when no file exists at the given path', function () {
    $invalidPath = getTestJpg().'non-existing';

    Image::load($invalidPath);
})->throws(CouldNotLoadImage::class);

it('will throw an exception when passing an invalid image driver name', function () {
    Image::useImageDriver('invalid')->load(getTestJpg());
})->throws(InvalidImageDriver::class);

it('can resize a gif without losing frames when Imagick is used', function () {
    $driver = Image::useImageDriver('imagick');
    $image = $driver->loadFile(getTestGif());
    $targetFile = $this->tempDir->path("{$driver->driverName()}/resize.gif");
    $numberOfFrames = count($image->image());
    expect($image->getHeight())->toEqual(320);

    $image->width(200)->save($targetFile);

    $targetImage = $driver->loadFile($targetFile);
    expect(count($targetImage->image()))->toBe($numberOfFrames);
    expect($targetImage->getWidth())->toEqual(200);
});

it('works with transparent pngs', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/saving-transparent-png.png");

    $driver->loadFile(getTestFile('transparent.png'))->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');
