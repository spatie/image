<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can adjust the contrast', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->contrast(100)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid contrast', function () {
    Image::load(getTestJpg())->contrast(101);
})->throws(InvalidManipulation::class);
