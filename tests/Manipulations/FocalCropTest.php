<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

it('can focal crop', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->focalCrop(100, 500, 100, 100)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('can focal crop with zoom', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->focalCrop(100, 500, 100, 100, 2)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid width', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->focalCrop(-100, 500, 100, 100);
});

it('will throw an exception when passing an zoom value not in range', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->focalCrop(100, 500, 100, 100, 900);
});
