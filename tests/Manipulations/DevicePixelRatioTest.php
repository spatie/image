<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can set the device pixel ratio', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->devicePixelRatio(2)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid device pixel ratio', function () {
    Image::load(getTestJpg())->devicePixelRatio(9);
})->throws(InvalidManipulation::class);
