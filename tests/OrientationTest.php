<?php

use Spatie\Image\Drivers\ImageDriver;

it('works with transparent pngs', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/saving-transparent-png.png");

    $original = $driver->loadFile(getTestFile('testOrientation.jpg'));

    expect($original->getWidth())->toEqual(340);
    expect($original->getHeight())->toEqual(280);

    $image = $original->save($targetFile);

    expect($image->getWidth())->toEqual(340);
    expect($image->getHeight())->toEqual(280);
})->with('drivers');
