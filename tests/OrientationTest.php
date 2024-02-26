<?php

use Spatie\Image\Drivers\ImageDriver;

it('keeps the correct orientation based on Exif data', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation.jpg");

    $original = $driver->loadFile(getTestFile('testOrientation.jpg'));

    expect($original->getWidth())->toEqual(340);
    expect($original->getHeight())->toEqual(280);

    $image = $original->save($targetFile);

    expect($image->getWidth())->toEqual(340);
    expect($image->getHeight())->toEqual(280);
})->with('drivers');
