<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can adjust the brightness', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->brightness(-75)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid brightness', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->brightness(-101);
});
