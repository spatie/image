<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can resize an image to specific width', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/width.png");

    $driver->loadFile(getTestJpg())->width(100)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');
