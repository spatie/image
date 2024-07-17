<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\InvalidManipulation;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can blur an image', function (ImageDriver $driver, int $blur) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/blur.png");

    $driver->loadFile(getTestJpg())->blur($blur)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers', [0, 50, 100]);

it('will throw an exception when passing an invalid blur value', function (ImageDriver $driver) {
    $driver->loadFile(getTestJpg())->blur(101);
})->with('drivers')->throws(InvalidManipulation::class);
