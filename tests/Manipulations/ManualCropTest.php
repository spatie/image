<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;

it('can contain an image in the given dimensions', function (
    array $dimensions,
    int $expectedWidth,
    int $expectedHeight,
) {
    $driver = Image::useImageDriver('gd');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/manual-crop.jpg");

    $driver->load(getTestJpg())->manualCrop(...$dimensions)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->load($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
})->with([
    [[1, 2,3,4], 0, 0],
]);
