<?php

use Spatie\Image\Drivers\ImageDriver;

it('can get a base64 string for an image', function (ImageDriver $driver) {
    $base64 = $driver->loadFile(getTestFile('transparent-bg.png'))->base64();

    expect($base64)->toBeString();
    expect($base64)->toStartWith('data:image/jpeg;base64,');
})->with('drivers');
