<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\InvalidManipulation;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can blur an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/blur.png");

    $driver->load(getTestJpg())->blur(50)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

it('will throw an exception when passing an invalid blur value', function (ImageDriver $driver) {
    $driver->load(getTestJpg())->blur(101);
})->with('drivers')->throws(InvalidManipulation::class);
