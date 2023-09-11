<?php

use Spatie\Image\Drivers\ImageDriver;
use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can contain an image in the given dimensions', function (
    ImageDriver $driver,
    array $dimensions,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/manual-crop.png");

    $driver->load(getTestJpg())->manualCrop(...$dimensions)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->load($targetFile);

    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[100, 50], 100, 50],
    [[100, 50, 0, 50], 100, 50],
    [[100, 50, 200, 200], 100, 50],
    [[1000, 50, 200, 200], 140, 50],
    [[100, 1000, 200, 200], 100, 80],
]);
