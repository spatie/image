<?php

use Spatie\Image\Drivers\ImageDriver;

it('can blur an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/sepia.jpg");

    $driver->load(getTestJpg())->sepia()->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');
