<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can resize an image to specific width', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/width.png");

    $driver->loadFile(getTestJpg())->width(100)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

it('keeps at least one pixel of height for images with an extreme aspect ratio', function (ImageDriver $driver) {
    $sourceFile = $this->tempDir->path("{$driver->driverName()}/wide-strip.png");
    imagepng(imagecreatetruecolor(588, 1), $sourceFile);

    $image = $driver->loadFile($sourceFile)->width(100);

    expect($image->getWidth())->toBe(100);
    expect($image->getHeight())->toBe(1);
})->with('drivers');
