<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can fit an image', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->fit(Manipulations::FIT_CONTAIN, 500, 300)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid fit', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->fit('blabla', 500, 300);
});
