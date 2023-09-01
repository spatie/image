<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\InvalidManipulation;

it('can apply gamma to an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/gamma.jpg");

    $driver->load(getTestJpg())->gamma(4.8)->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');
