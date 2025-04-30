<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can manually crop to the given dimensions', function (
    ImageDriver $driver,
    array $dimensions,
    int $expectedWidth,
    int $expectedHeight,
) {
    $fileName = 'manual-crop-'.implode('-', $dimensions)."-{$expectedWidth}-{$expectedHeight}.png";

    $targetFile = $this->tempDir->path("{$driver->driverName()}/{$fileName}");

    $driver->loadFile(getTestJpg())->manualCrop(...$dimensions)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->loadFile($targetFile);

    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[100, 50], 100, 50],
    [[100, 50, 0, 50], 100, 50],
    [[1000, 50, 200, 200], 140, 50],
    [[100, 50, 200, 200], 100, 50],
    [[100, 1000, 200, 200], 100, 80],
]);
