<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Image;

it('can crop an image relative to a position', function(
    ImageDriver $driver,
    array $cropArguments,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/manual-crop.jpg");

    $driver->load(getTestJpg())->crop(... $cropArguments)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->load($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
})->with('drivers')->with([
    [[50, 100, CropPosition::TopLeft], 50, 100],
]);
