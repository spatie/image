<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can set the height', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->height(100)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid height', function () {
    Image::load(getTestJpg())->height(-10);
})->throws(InvalidManipulation::class);
