<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;

beforeEach(fn() => true)->skip();

it('can make an image sepia', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->sepia()->save($targetFile);

    expect($targetFile)->toBeFile();
});
