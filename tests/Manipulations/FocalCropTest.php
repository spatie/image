<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can perform a crop centered around given coordinates', function (
    ImageDriver $driver,
    array $focalCropArguments,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/manual-crop.png");

    $driver->load(getTestJpg())->focalCrop(...$focalCropArguments)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->load($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[100, 100, 60, 60], 100, 100],
]);
