<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can adjust the gamma', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->gamma(9.5)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid gamma', function () {
    Image::load(getTestJpg())->gamma(101);
})->throws(InvalidManipulation::class);
