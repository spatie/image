<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can set the orientation', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->orientation(Manipulations::ORIENTATION_90)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid orientation', function () {
    Image::load(getTestJpg())->orientation('blabla');
})->throws(InvalidManipulation::class);
