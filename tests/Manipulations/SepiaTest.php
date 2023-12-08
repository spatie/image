<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can sepia an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/sepia.png");

    $driver->loadFile(getTestJpg())->sepia()->save($targetFile);

    assertMatchesImageSnapshot($targetFile, 0.3);
})->with('drivers');
