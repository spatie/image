<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can blur', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->blur(5)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid blur value', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->blur(1000);
});
