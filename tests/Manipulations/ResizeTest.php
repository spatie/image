<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Image;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can resize an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/resize.png");

    $image = $driver->loadFile(getTestJpg())->resize(100, 70)->save($targetFile);

    expect($image->getWidth())->toBe(100);
    expect($image->getHeight())->toBe(70);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

it('can resize canvas of transparent PNGs without loosing transparency when GD is used', function (ImageDriver $driver) {
    if ($driver->driverName() !== 'gd') {
        return $this->expectNotToPerformAssertions();
    }

    $targetFile = $this->tempDir->path("{$driver->driverName()}/resize-with-transparent-png.png");
    $image = $driver->loadFile(getTestFile('transparent.png')); // 1640x923
    $transparentColor = imagecolorallocatealpha($image->image(), 255, 255, 255, 127);

    expect($image->getHeight())->toEqual(923);
    expect($image->pickColor(0, 0, \Spatie\Image\Enums\ColorFormat::Int))->toEqual($transparentColor);

    // make it square, so height is added top and bottom, bg transparent
    $image->resizeCanvas(1640, 1640, \Spatie\Image\Enums\AlignPosition::Center, false, $image->pickColor(0, 0, \Spatie\Image\Enums\ColorFormat::Rgba))->save($targetFile);

    $targetImage = $driver->loadFile($targetFile);
    expect($targetImage->getHeight())->toEqual(1640);
    expect($targetImage->pickColor(0, 0, \Spatie\Image\Enums\ColorFormat::Int))->toEqual($transparentColor);
})->with('drivers');
