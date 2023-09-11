<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Orientation;

it('can blur an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation-90.jpg");
    $driver->load(getTestFile('portrait.jpg'))->orientation(Orientation::ROTATE_90)->save($targetFile);
    expect($targetFile)->toBeFile();

    $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation-180.jpg");
    $driver->load(getTestFile('portrait.jpg'))->orientation(Orientation::ROTATE_180)->save($targetFile);
    expect($targetFile)->toBeFile();

    $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation-270.jpg");
    $driver->load(getTestFile('portrait.jpg'))->orientation(Orientation::ROTATE_270)->save($targetFile);
    expect($targetFile)->toBeFile();

    $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation-auto.jpg");
    $driver->load(getTestFile('portrait.jpg'))->orientation()->save($targetFile);
    expect($targetFile)->toBeFile();
})->with('drivers');
