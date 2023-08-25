<?php

use Spatie\Image\Image;

it('can get the height of an image', function () {
    $image = Image::load(getTestJpg());

    expect($image->getHeight())->toBe(280);
});
