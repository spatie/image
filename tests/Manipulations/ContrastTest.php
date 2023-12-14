<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\InvalidManipulation;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can change the contrast of an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/contrast.png");

    $driver->loadFile(getTestPhoto())->contrast(30)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

it('will throw an exception when passing an invalid contrast value', function (ImageDriver $driver) {
    $driver->loadFile(getTestPhoto())->brightness(101);
})->with('drivers')->throws(InvalidManipulation::class);
