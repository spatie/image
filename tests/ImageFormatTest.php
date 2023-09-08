<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\UnsupportedImageFormat;

it('can save a jpeg', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.jpeg");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');

it('can save a png', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.png");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');

it('can save a webp', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.webp");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');

it('can save a gif', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.gif");

    $driver->load(getTestJpg())->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');

it('can not save a bogus extension', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/format-test.foobar");

    $driver->load(getTestJpg())->save($targetFile);
})->with('drivers')->throws(UnsupportedImageFormat::class);