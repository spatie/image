<?php


use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can colorize an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/width.png");

    $driver->load(getTestJpg())->width(100)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');
