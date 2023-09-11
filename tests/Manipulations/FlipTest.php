<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\FlipDirection;

it('can blur an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/flip-h.jpg");
    $driver->load(getTestJpg())->flip(FlipDirection::HORIZONTALLY)->save($targetFile);
    expect($targetFile)->toBeFile();

    $targetFile = $this->tempDir->path("{$driver->driverName()}/flip-v.jpg");
    $driver->load(getTestJpg())->flip(FlipDirection::VERTICALLY)->save($targetFile);
    expect($targetFile)->toBeFile();

    $targetFile = $this->tempDir->path("{$driver->driverName()}/flip-b.jpg");
    $driver->load(getTestJpg())->flip(FlipDirection::BOTH)->save($targetFile);
    expect($targetFile)->toBeFile();
})->with('drivers');
