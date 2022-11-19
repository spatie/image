<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

it('can manual crop', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->manualCrop(100, 500, 30, 30)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid width', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->manualCrop(-100, 500, 100, 100);
});
