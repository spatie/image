<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can set the device pixel ratio', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->devicePixelRatio(2)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid device pixel ratio', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->devicePixelRatio(9);
});
