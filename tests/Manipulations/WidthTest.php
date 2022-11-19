<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

it('can set the width', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->width(100)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid width', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->width(-10);
});
