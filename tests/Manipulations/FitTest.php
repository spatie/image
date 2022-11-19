<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can fit an image', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->fit(Manipulations::FIT_CONTAIN, 500, 300)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid fit', function () {
    Image::load(getTestJpg())->fit('blabla', 500, 300);
})->throws(InvalidManipulation::class);
