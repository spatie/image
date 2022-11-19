<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can add a border to an image', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->border(10, 'black', Manipulations::BORDER_OVERLAY)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing a wrong border type', function () {
    Image::load(getTestJpg())->border(10, 'black', 'blabla');
})->throws(InvalidManipulation::class);
