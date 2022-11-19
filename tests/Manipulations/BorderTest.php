<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can add a border to an image', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())->border(10, 'black', Manipulations::BORDER_OVERLAY)->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('will throw an exception when passing a wrong border type', function () {
    $this->expectException(InvalidManipulation::class);

    Image::load($this->getTestJpg())->border(10, 'black', 'blabla');
});
