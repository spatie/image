<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can pixelate', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->pixelate(50)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid pixelate value', function () {
    Image::load(getTestJpg())->pixelate(1001);
})->throws(InvalidManipulation::class);
