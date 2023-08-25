<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;

beforeEach(fn() => true)->skip();

it('can flip an image', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->flip(Manipulations::FLIP_HORIZONTALLY)->save($targetFile);

    expect($targetFile)->toBeFile();
});
