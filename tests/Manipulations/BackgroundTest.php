<?php

use Spatie\Image\Drivers\ImageDriver;

it('sets the background on a png with a transparent background', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/background.png");

    $driver->load(getTestFile('transparent-bg.png'))->background('#ff5733')->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');
