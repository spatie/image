<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\CropPosition;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can crop an image relative to a position', function (
    ImageDriver $driver,
    array $cropArguments,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/manual-crop.png");

    $driver->load(getTestJpg())->crop(...$cropArguments)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->load($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[50, 100, CropPosition::TopLeft], 50, 100],
    [[50, 100, CropPosition::Center], 50, 100],
    [[50, 100, CropPosition::Top], 50, 100],
    [[50, 100, CropPosition::Left], 50, 100],
    [[50, 100, CropPosition::Right], 50, 100],

]);
