<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can crop', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->crop(Manipulations::CROP_BOTTOM, 100, 500)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid crop method', function () {
    Image::load(getTestJpg())->crop('blabla', 10, 10);
})->throws(InvalidManipulation::class);

it('will throw an exception when passing a negative width', function () {
    Image::load(getTestJpg())->crop(Manipulations::CROP_BOTTOM, -10, 10);
})->throws(InvalidManipulation::class);

it('will throw an exception when passing a negative height', function () {
    Image::load(getTestJpg())->crop(Manipulations::CROP_BOTTOM, 10, -10);
})->throws(InvalidManipulation::class);
