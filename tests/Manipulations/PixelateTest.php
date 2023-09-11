<?php

use Spatie\Image\Drivers\ImageDriver;

it('can blur an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/pixelate.png");

    $driver->load(getTestJpg())->pixelate()->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');
