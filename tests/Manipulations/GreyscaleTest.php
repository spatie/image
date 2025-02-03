<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can greyscale an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/greyscale.png");

    $driver->loadFile(getTestJpg())->greyscale()->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');
