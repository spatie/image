<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Image;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('keeps the correct orientation based on Exif data', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation.jpg");

    $original = $driver->loadFile(getTestFile('testOrientation.jpg'));

    expect($original->getWidth())->toEqual(340);
    expect($original->getHeight())->toEqual(280);

    $image = $original->save($targetFile);

    expect($image->getWidth())->toEqual(340);
    expect($image->getHeight())->toEqual(280);
})->with('drivers');

it('handles all EXIF orientations correctly', function (ImageDriver $driver, int $orientation) {
    $sourceFile = getTestFile('testOrientation.jpg');

    // Create a canonical image by auto-orienting the source with Imagick
    $canonical = new Imagick($sourceFile);
    $canonical->setImageOrientation($canonical->getImageOrientation());
    $canonical->autoOrient();

    // Apply the reverse transformation so autoRotate will produce the canonical result
    $img = clone $canonical;

    match ($orientation) {
        1 => null,
        2 => $img->flopImage(),
        3 => $img->rotateImage('#000', 180),
        4 => $img->rotateImage('#000', 180) && $img->flopImage(),
        5 => $img->rotateImage('#000', 90) && $img->flopImage(),
        6 => $img->rotateImage('#000', -90),
        7 => $img->rotateImage('#000', -90) && $img->flopImage(),
        8 => $img->rotateImage('#000', 90),
    };

    $img->setImageOrientation($orientation);

    $orientedFile = $this->tempDir->path("orientation-source-{$orientation}.jpg");
    $img->writeImage($orientedFile);
    $img->destroy();
    $canonical->destroy();

    // Load with the driver under test (triggers autoRotate) and save
    $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation-{$orientation}.png");
    $driver->loadFile($orientedFile)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with(range(1, 8));
