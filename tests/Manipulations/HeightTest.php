<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can can resize the image to a certain height', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/height.png");

    $driver->loadFile(getTestJpg())->height(100)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

it('keeps at least one pixel of width for images with an extreme aspect ratio', function (ImageDriver $driver) {
    $sourceFile = $this->tempDir->path("{$driver->driverName()}/tall-strip.png");
    imagepng(imagecreatetruecolor(1, 588), $sourceFile);

    $image = $driver->loadFile($sourceFile)->height(100);

    expect($image->getWidth())->toBe(1);
    expect($image->getHeight())->toBe(100);
})->with('drivers');
