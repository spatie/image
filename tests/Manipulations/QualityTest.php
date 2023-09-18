<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can set the quality of a png', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/quality.png");

    $driver->load(getTestJpg())->quality(20)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

it('can set the quality for different formats', function (ImageDriver $driver, string $format) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/quality.{$format}");

    $driver->load(getTestJpg())->quality(20)->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers', ['jpg', 'gif', 'webp']);
