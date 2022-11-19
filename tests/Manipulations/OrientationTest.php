<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Test\TestCase;

it('can set the orientation', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->orientation(Manipulations::ORIENTATION_90)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing an invalid orientation', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->orientation('blabla');
});
