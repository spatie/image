<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;

it('can make an image greyscale', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->greyscale()->save($targetFile);

    expect($targetFile)->toBeFile();
});
