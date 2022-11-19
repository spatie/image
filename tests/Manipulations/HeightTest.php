<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

it('can set the height', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->height(100)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid height', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->height(-10);
});
