<?php

use Spatie\Image\Drivers\ImageDriver;

it('can set the quality of an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/quality.png");

    $driver->load(getTestJpg())->quality(20)->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');
