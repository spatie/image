<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\InvalidManipulation;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can colorize an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/colorize.png");

    $driver->load(getTestJpg())->colorize(10, 70, 10)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

it('will throw an exception when passing an invalid blur value', function (ImageDriver $driver) {
    $driver->load(getTestJpg())->colorize(101, 100, 100);
})->with('drivers')->throws(InvalidManipulation::class);
