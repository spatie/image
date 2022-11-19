<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can crop', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->crop(Manipulations::CROP_BOTTOM, 100, 500)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid crop method', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->crop('blabla', 10, 10);
});

it('will throw an exception when passing a negative width', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->crop(Manipulations::CROP_BOTTOM, -10, 10);
});

it('will throw an exception when passing a negative height', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->crop(Manipulations::CROP_BOTTOM, 10, -10);
});
