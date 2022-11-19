<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can adjust the contrast', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->contrast(100)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid contrast', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->contrast(101);
});
