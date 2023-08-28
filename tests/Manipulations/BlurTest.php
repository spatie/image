<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\InvalidManipulation;

it('can blur an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/blur.jpg");

    $driver->load(getTestJpg())->blur(50)->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');

it('will throw an exception when passing an invalid blur value', function (ImageDriver $driver) {
    $driver->load(getTestJpg())->brightness(101);
})->with('drivers')->throws(InvalidManipulation::class);
