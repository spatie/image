<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can set the width', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->width(100)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid width', function () {
    Image::load(getTestJpg())->width(-10);
})->throws(InvalidManipulation::class);
