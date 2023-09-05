<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\InvalidManipulation;

it('can change the contrast of an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/contrast.jpg");

    $driver->load(getTestPhoto())->contrast(30)->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');

it('will throw an exception when passing an invalid contrast value', function (ImageDriver $driver) {
    $driver->load(getTestPhoto())->brightness(101);
})->with('drivers')->throws(InvalidManipulation::class);
