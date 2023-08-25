<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Image;

it('can get the height of an image', function (ImageDriver $driver) {
    $image = $driver->load(getTestJpg());

    expect($image->getHeight())->toBe(280);
})->with('drivers');

dataset('drivers', [
    'imagick' => [Image::useImageDriver('imagick')],
    'gd' => [Image::useImageDriver('gd')],
]);
