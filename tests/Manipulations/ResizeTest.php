<?php


use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can colorize an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/resize.png");

    $driver->load(getTestJpg())->resize(100, 70)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');
