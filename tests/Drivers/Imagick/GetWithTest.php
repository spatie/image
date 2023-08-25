<?php

use Spatie\Image\Image;

it('can get the width of an image', function () {
    $image = Image::load(getTestJpg());

    expect($image->getWidth())->toBe(340);
});
