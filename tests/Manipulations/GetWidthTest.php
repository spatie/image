<?php

use Spatie\Image\Drivers\ImageDriver;

it('can get the width of an image', function (ImageDriver $driver) {
    $image = $driver->loadFile(getTestJpg());

    expect($image->getWidth())->toBe(340);
})->with('drivers');
