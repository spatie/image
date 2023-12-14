<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can pixelate an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/pixelate.png");

    $driver->loadFile(getTestJpg())->pixelate()->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');
