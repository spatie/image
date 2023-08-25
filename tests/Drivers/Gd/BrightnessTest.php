<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can adjust the brightness', function () {
    $targetFile = $this->tempDir->path('gd/conversion.jpg');

    Image::useImageDriver('gd')->load(getTestJpg())->brightness(-50)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid brightness', function () {
    Image::load(getTestJpg())->brightness(-101);
})->throws(InvalidManipulation::class);