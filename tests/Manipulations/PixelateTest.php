<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

it('can pixelate', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->pixelate(50)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid pixelate value', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->pixelate(1001);
});
