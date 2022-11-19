<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can manual crop', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->manualCrop(100, 500, 30, 30)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid width', function () {
    Image::load(getTestJpg())->manualCrop(-100, 500, 100, 100);
})->throws(InvalidManipulation::class);
