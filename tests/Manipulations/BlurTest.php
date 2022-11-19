<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can blur', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->blur(5)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid blur value', function () {
    Image::load(getTestJpg())->blur(1000);
})->throws(InvalidManipulation::class);
