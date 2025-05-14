<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can can resize the image to a certain height', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/height.png");

    $driver->loadFile(getTestJpg())->height(100)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');
