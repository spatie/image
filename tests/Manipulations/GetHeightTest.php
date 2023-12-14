<?php

use Spatie\Image\Drivers\ImageDriver;

it('can get the height of an image', function (ImageDriver $driver) {
    $image = $driver->loadFile(getTestJpg());

    expect($image->getHeight())->toBe(280);
})->with('drivers');
