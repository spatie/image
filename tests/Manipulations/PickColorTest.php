<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\ColorFormat;

it('can pick the color of a pixel', function (ImageDriver $driver) {
    $image = $driver->loadFile(getTestJpg());

    expect($image->pickColor(30, 30, ColorFormat::Array))->toBe([
        102, 135, 106, 1.0,
    ]);
})->with('drivers');
