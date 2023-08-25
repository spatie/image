<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can adjust the brightness', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path('imagick/conversion.jpg');

    $driver->load(getTestJpg())->brightness(-50)->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers');


it('will throw an exception when passing an invalid brightness', function (ImageDriver $driver) {
    $driver->load(getTestJpg())->brightness(-101);
})->with('drivers')->throws(InvalidManipulation::class);

dataset('drivers', [
    'imagick' => [Image::useImageDriver('imagick')],
    'gd' => [Image::useImageDriver('gd')],
]);



