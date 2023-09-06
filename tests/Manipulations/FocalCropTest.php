<?php

use Spatie\Image\Drivers\ImageDriver;

it('can perform a crop centered around given coordinates', function (
    ImageDriver $driver,
    array $focalCropArguments,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/manual-crop.jpg");

    $driver->load(getTestJpg())->focalCrop(...$focalCropArguments)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->load($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
})->with('drivers')->with([
    [[100, 100, 60, 60], 100, 100],
]);
