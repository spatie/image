<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\InvalidManipulation;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can pixelate an image', function (ImageDriver $driver, int $pixelate) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/pixelate.png");

    $driver->loadFile(getTestJpg())->pixelate($pixelate)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers', [0, 50, 100]);

it('will throw an exception when passing an invalid pixelate value', function (ImageDriver $driver) {
    $driver->loadFile(getTestJpg())->pixelate(101);
})->with('drivers')->throws(InvalidManipulation::class);
