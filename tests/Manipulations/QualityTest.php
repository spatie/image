<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can set the quality of a png', function (ImageDriver $driver, int $quality) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/quality.png");

    $driver->load(getTestJpg())->quality($quality)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([10, 50, 90]);

it('can set the quality for different formats', function (ImageDriver $driver, string $format, int $quality) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/quality.{$format}");

    $driver->load(getTestJpg())->quality($quality)->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers')->with(['jpg', 'png', 'webp'])->with([10, 50, 90]);
